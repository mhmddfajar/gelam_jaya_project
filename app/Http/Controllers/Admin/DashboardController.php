<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Hamlet;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard overview.
     */
    public function index(): Response
    {
        $totalResidents = Resident::count();
        $verifiedResidents = Resident::where('is_verified', true)->count();
        $unverifiedResidents = Resident::where('is_verified', false)->count();

        // Hamlet distribution
        $hamlets = Hamlet::withCount('residents')->orderBy('name')->get();
        $hamletDistribution = $hamlets->map(function ($hamlet) use ($totalResidents) {
            $percentage = $totalResidents > 0 ? round(($hamlet->residents_count / $totalResidents) * 100, 1) : 0;
            return [
                'id' => $hamlet->id,
                'name' => $hamlet->name,
                'head_name' => $hamlet->head_name,
                'count' => $hamlet->residents_count,
                'percentage' => $percentage,
            ];
        });

        // Request metrics (safely handled before & after Fase 5 migration)
        $hasLetterRequests = Schema::hasTable('letter_requests');
        $requestsSummary = [
            'today_total' => 0,
            'daring_total' => 0,
            'luring_total' => 0,
            'diajukan' => 0,
            'diproses' => 0,
            'siap_diambil' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];
        $recentRequests = [];

        if ($hasLetterRequests) {
            // Query dynamically when table exists
            $requestsSummary['today_total'] = \DB::table('letter_requests')->whereDate('created_at', today())->count();
            $requestsSummary['daring_total'] = \DB::table('letter_requests')->where('channel', 'daring')->count();
            $requestsSummary['luring_total'] = \DB::table('letter_requests')->where('channel', 'luring')->count();
            $requestsSummary['diajukan'] = \DB::table('letter_requests')->where('status', 'diajukan')->count();
            $requestsSummary['diproses'] = \DB::table('letter_requests')->where('status', 'diproses')->count();
            $requestsSummary['siap_diambil'] = \DB::table('letter_requests')->where('status', 'siap_diambil')->count();
            $requestsSummary['selesai'] = \DB::table('letter_requests')->where('status', 'selesai')->count();
            $requestsSummary['ditolak'] = \DB::table('letter_requests')->where('status', 'ditolak')->count();

            $recentRequests = \DB::table('letter_requests')
                ->join('residents', 'letter_requests.resident_id', '=', 'residents.id')
                ->leftJoin('letter_types', 'letter_requests.letter_type_id', '=', 'letter_types.id')
                ->select(
                    'letter_requests.id',
                    'letter_requests.registration_no',
                    'letter_requests.channel',
                    'letter_requests.status',
                    'letter_requests.created_at',
                    'residents.name as resident_name',
                    'letter_types.name as letter_type_name'
                )
                ->latest('letter_requests.created_at')
                ->limit(5)
                ->get();
        }

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'today_requests' => $requestsSummary['today_total'],
                'daring_requests' => $requestsSummary['daring_total'],
                'luring_requests' => $requestsSummary['luring_total'],
                'total_residents' => $totalResidents,
                'verified_residents' => $verifiedResidents,
                'unverified_residents' => $unverifiedResidents,
                'total_users' => User::count(),
                'total_staf' => User::where('role', Role::STAF)->count(),
            ],
            'requestStats' => $requestsSummary,
            'hamletDistribution' => $hamletDistribution,
            'recentRequests' => $recentRequests,
            'hasLetterRequests' => $hasLetterRequests,
        ]);
    }
}
