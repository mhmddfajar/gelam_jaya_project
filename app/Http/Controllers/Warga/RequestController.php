<?php

namespace App\Http\Controllers\Warga;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warga\StoreLetterRequest;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\RequestAttachment;
use App\Models\RequestStatusLog;
use App\Services\RegistrationNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestController extends Controller
{
    /**
     * Display list of citizen's letter requests.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = LetterRequest::where('resident_id', $user->resident_id)
            ->with(['letterType'])
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('letterType', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        // Calculate summary counts
        $baseQuery = LetterRequest::where('resident_id', $user->resident_id);
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'completed' => (clone $baseQuery)->where('status', RequestStatus::SELESAI->value)->count(),
            'rejected' => (clone $baseQuery)->where('status', RequestStatus::DITOLAK->value)->count(),
        ];

        return Inertia::render('Warga/Requests/Index', [
            'requests' => $requests,
            'filters' => $request->only(['status', 'search']),
            'stats' => $stats,
        ]);
    }

    /**
     * Show form to create a new daring letter request.
     */
    public function create(Request $request): Response
    {
        $letterTypes = LetterType::active()
            ->with('typeRequirements')
            ->orderBy('name')
            ->get();

        $selectedTypeId = $request->query('letter_type_id') ?: $request->query('type');
        $selectedType = null;

        if ($selectedTypeId) {
            $selectedType = $letterTypes->first(function ($t) use ($selectedTypeId) {
                return (string) $t->id === (string) $selectedTypeId ||
                       strtoupper($t->code) === strtoupper((string) $selectedTypeId);
            });
        }

        return Inertia::render('Warga/Requests/Create', [
            'letterTypes' => $letterTypes,
            'preselectedType' => $selectedType,
            'resident' => $request->user()->resident,
        ]);
    }

    /**
     * Store newly submitted daring letter request.
     */
    public function store(StoreLetterRequest $request, RegistrationNumberService $registrationNumberService): RedirectResponse
    {
        $user = $request->user();
        $letterTypeId = (int) $request->input('letter_type_id');
        $letterType = LetterType::with('typeRequirements')->findOrFail($letterTypeId);

        $letterRequest = DB::transaction(function () use ($request, $user, $letterType, $registrationNumberService) {
            $registrationNo = $registrationNumberService->generate();

            $newRequest = LetterRequest::create([
                'registration_no' => $registrationNo,
                'letter_type_id' => $letterType->id,
                'resident_id' => $user->resident_id,
                'submitted_by' => $user->id,
                'channel' => Channel::DARING,
                'status' => RequestStatus::DIAJUKAN,
                'purpose' => trim($request->input('purpose')),
                'form_data' => $request->input('form_data', []),
            ]);

            // Save attachments to private storage
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $requirementsMap = $letterType->typeRequirements->keyBy('id');

                foreach ($files as $key => $file) {
                    if (!$file || !$file->isValid()) {
                        continue;
                    }

                    // Check if key matches requirement ID
                    $requirementId = null;
                    if (is_numeric($key) && isset($requirementsMap[$key])) {
                        $requirementId = (int) $key;
                    }

                    $storedPath = $file->store("attachments/{$newRequest->id}", 'local');
                    $sizeKb = (int) round($file->getSize() / 1024);

                    RequestAttachment::create([
                        'letter_request_id' => $newRequest->id,
                        'requirement_id' => $requirementId,
                        'file_path' => $storedPath,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                        'size_kb' => max(1, $sizeKb),
                    ]);
                }
            }

            // Create initial status log
            RequestStatusLog::create([
                'letter_request_id' => $newRequest->id,
                'from_status' => null,
                'to_status' => RequestStatus::DIAJUKAN->value,
                'note' => 'Permohonan surat berhasil dibuat secara daring oleh warga.',
                'changed_by' => $user->id,
                'created_at' => now(),
            ]);

            return $newRequest;
        });

        return redirect()->route('warga.requests.show', $letterRequest->id)
            ->with('success', "Permohonan surat berhasil diajukan dengan nomor registrasi {$letterRequest->registration_no}.");
    }

    /**
     * Show detail of a letter request.
     */
    public function show(Request $request, LetterRequest $letter_request): Response
    {
        Gate::authorize('view', $letter_request);

        $letter_request->load([
            'letterType.typeRequirements',
            'resident.hamlet',
            'attachments.requirement',
            'statusLogs.changer',
        ]);

        return Inertia::render('Warga/Requests/Show', [
            'letterRequest' => $letter_request,
        ]);
    }

    /**
     * Download an attachment safely through policy check.
     */
    public function downloadAttachment(Request $request, LetterRequest $letter_request, RequestAttachment $attachment): StreamedResponse
    {
        Gate::authorize('downloadAttachment', [$letter_request, $attachment]);

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'Berkas lampiran tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('local')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }
}
