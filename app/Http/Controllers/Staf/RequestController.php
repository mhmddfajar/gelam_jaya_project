<?php

namespace App\Http\Controllers\Staf;

use App\Enums\Channel;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staf\StoreOfflineLetterRequest;
use App\Http\Requests\Staf\UpdateStatusRequest;
use App\Models\Hamlet;
use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\RequestAttachment;
use App\Models\Resident;
use App\Services\RegistrationNumberService;
use App\Services\RequestStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestController extends Controller
{
    /**
     * Display a queue of all letter requests (daring and luring).
     */
    public function index(Request $request): Response
    {
        $query = LetterRequest::with(['resident.hamlet', 'letterType', 'submitter'])
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by channel
        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        // Search query
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('registration_no', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhereHas('resident', function ($rq) use ($search) {
                      $rq->where('name', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  })
                  ->orWhereHas('letterType', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        // Summary statistics
        $stats = [
            'total' => LetterRequest::count(),
            'diajukan' => LetterRequest::where('status', RequestStatus::DIAJUKAN->value)->count(),
            'diverifikasi' => LetterRequest::where('status', RequestStatus::DIVERIFIKASI->value)->count(),
            'diproses' => LetterRequest::where('status', RequestStatus::DIPROSES->value)->count(),
            'siap_diambil' => LetterRequest::where('status', RequestStatus::SIAP_DIAMBIL->value)->count(),
            'selesai' => LetterRequest::where('status', RequestStatus::SELESAI->value)->count(),
            'ditolak' => LetterRequest::where('status', RequestStatus::DITOLAK->value)->count(),
            'daring' => LetterRequest::where('channel', Channel::DARING->value)->count(),
            'luring' => LetterRequest::where('channel', Channel::LURING->value)->count(),
        ];

        $statuses = array_map(fn (RequestStatus $status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'badgeColor' => $status->badgeColor(),
        ], RequestStatus::cases());

        $channels = array_map(fn (Channel $channel) => [
            'value' => $channel->value,
            'label' => $channel->label(),
        ], Channel::cases());

        return Inertia::render('Staf/Requests/Index', [
            'requests' => $requests,
            'filters' => $request->only(['status', 'channel', 'search']),
            'stats' => $stats,
            'statuses' => $statuses,
            'channels' => $channels,
        ]);
    }

    /**
     * Show form to create a new offline (luring) request.
     */
    public function create(Request $request): Response
    {
        $residents = Resident::withTrashed(false)
            ->with('hamlet')
            ->select('id', 'nik', 'name', 'address', 'rt', 'rw', 'hamlet_id', 'is_verified', 'birth_date', 'gender')
            ->orderBy('name')
            ->get();

        $letterTypes = LetterType::active()
            ->with('typeRequirements')
            ->orderBy('name')
            ->get();

        $hamlets = Hamlet::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Staf/Requests/Create', [
            'residents' => $residents,
            'letterTypes' => $letterTypes,
            'hamlets' => $hamlets,
            'preselectedResidentId' => $request->query('resident_id'),
        ]);
    }

    /**
     * Store newly submitted offline (luring) letter request.
     */
    public function store(
        StoreOfflineLetterRequest $request,
        RegistrationNumberService $regNumberService,
        RequestStatusService $statusService
    ): RedirectResponse {
        $letterTypeId = (int) $request->input('letter_type_id');
        $letterType = LetterType::with('typeRequirements')->findOrFail($letterTypeId);

        $letterRequest = DB::transaction(function () use ($request, $letterType, $regNumberService, $statusService) {
            $registrationNo = $regNumberService->generate();

            $newRequest = LetterRequest::create([
                'registration_no' => $registrationNo,
                'letter_type_id' => $letterType->id,
                'resident_id' => (int) $request->input('resident_id'),
                'submitted_by' => $request->user()->id,
                'channel' => Channel::LURING,
                'status' => RequestStatus::DIPROSES,
                'purpose' => trim($request->input('purpose')),
                'form_data' => $request->input('form_data', []),
            ]);

            // Save attachments if provided
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $requirementsMap = $letterType->typeRequirements->keyBy('id');

                foreach ($files as $key => $file) {
                    if (!$file || !$file->isValid()) {
                        continue;
                    }

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

            // Record initial log
            $statusService->createInitialLog(
                $newRequest,
                $request->user(),
                'Permohonan surat didaftarkan secara luring di loket pelayanan kantor desa oleh petugas.'
            );

            return $newRequest;
        });

        return redirect()->route('staf.requests.show', $letterRequest->id)
            ->with('success', "Permohonan surat luring berhasil didaftarkan dengan nomor registrasi {$letterRequest->registration_no}.");
    }

    /**
     * Show detail of a letter request along with status control panel.
     */
    public function show(LetterRequest $letter_request, RequestStatusService $statusService): Response
    {
        $letter_request->load([
            'resident.hamlet',
            'letterType.typeRequirements',
            'attachments.requirement',
            'statusLogs.changer',
            'submitter',
            'signer',
        ]);

        $allowedStatuses = $statusService->getAllowedNextStatuses($letter_request, request()->user());

        return Inertia::render('Staf/Requests/Show', [
            'letterRequest' => $letter_request,
            'allowedStatuses' => $allowedStatuses,
        ]);
    }

    /**
     * Update status of a letter request.
     */
    public function updateStatus(
        UpdateStatusRequest $request,
        LetterRequest $letter_request,
        RequestStatusService $statusService
    ): RedirectResponse {
        $statusService->transition(
            $letter_request,
            $request->input('status'),
            $request->user(),
            $request->input('note')
        );

        return back()->with('success', 'Status permohonan surat berhasil diperbarui.');
    }

    /**
     * Download attachment file securely for staff/admin.
     */
    public function downloadAttachment(RequestAttachment $attachment): StreamedResponse
    {
        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'Berkas lampiran tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('local')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }
}
