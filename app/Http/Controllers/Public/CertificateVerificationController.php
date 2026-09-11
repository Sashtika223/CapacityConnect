<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function verify(Request $request, ?string $code = null): View
    {
        $verificationCode = $code ?? $request->input('code');
        $certificate = null;
        $searched = false;

        if ($verificationCode) {
            $searched = true;
            $certificate = Certificate::with(['course.department', 'course.trainer'])
                ->where('verification_code', $verificationCode)
                ->orWhere('certificate_number', $verificationCode)
                ->first();
        }

        return view('public.certificates.verify', compact('certificate', 'verificationCode', 'searched'));
    }
}
