<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class TraineeCertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Auth::user()->certificates()
            ->with(['course.trainer', 'course.department'])
            ->latest('issue_date')
            ->get();

        return view('trainee.certificates.index', compact('certificates'));
    }

    public function show(Certificate $certificate): View
    {
        if ($certificate->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $certificate->load(['course.trainer', 'course.department', 'user.profile']);

        return view('trainee.certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate): Response
    {
        if ($certificate->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $certificate->load(['course.trainer', 'course.department', 'user.profile']);
        $html = view('trainee.certificates.show', compact('certificate'))->render();

        return response()->streamDownload(function () use ($html): void {
            echo $html;
        }, "certificate-{$certificate->certificate_number}.html", [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
