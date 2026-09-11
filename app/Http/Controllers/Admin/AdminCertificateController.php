<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCertificateController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = Certificate::with(['user.department', 'course.trainer'])->latest();

        if ($status === 'expired') {
            $query->where('status', 'expired');
        } elseif ($status === 'expiring_soon') {
            $query->where('status', 'expiring_soon');
        } elseif ($status === 'valid') {
            $query->where('status', 'valid');
        }

        $certificates = $query->paginate(20);

        $stats = [
            'total' => Certificate::count(),
            'valid' => Certificate::where('status', 'valid')->count(),
            'expiring_soon' => Certificate::where('status', 'expiring_soon')->count(),
            'expired' => Certificate::where('status', 'expired')->count(),
        ];

        return view('admin.certificates.index', compact('certificates', 'stats', 'status'));
    }

    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $reason = $request->input('reason', 'Administrative revocation due to governance audit.');
        $certificate->update([
            'status' => 'revoked',
            'revocation_reason' => $reason,
        ]);

        AuditLogService::log('certificate.revoked', $certificate, null, [
            'certificate_number' => $certificate->certificate_number,
            'reason' => $reason,
        ]);

        return back()->with('info', "Certificate {$certificate->certificate_number} has been revoked.");
    }
}
