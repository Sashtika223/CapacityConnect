<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Certificate;
use App\Models\Competency;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredCourses = Course::with(['trainer', 'department', 'competencies'])
            ->where('status', 'published')
            ->latest('published_at')
            ->take(6)
            ->get();

        $departments = Department::withCount('courses')->take(6)->get();
        $competencies = Competency::take(8)->get();
        $announcements = Announcement::whereIn('target_role', ['all', 'trainee'])->latest('published_at')->take(3)->get();

        $stats = [
            'trainees_count' => User::where('role', 'trainee')->count(),
            'trainers_count' => User::where('role', 'trainer')->where('status', 'active')->count(),
            'courses_count' => Course::where('status', 'published')->count(),
            'certificates_issued' => Certificate::where('status', 'valid')->count(),
        ];

        return view('public.home', compact('featuredCourses', 'departments', 'competencies', 'announcements', 'stats'));
    }
}
