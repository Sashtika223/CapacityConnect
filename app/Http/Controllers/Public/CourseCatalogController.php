<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['trainer', 'department', 'competencies'])
            ->where('status', 'published');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->input('department'));
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('competency')) {
            $query->whereHas('competencies', function ($q) use ($request) {
                $q->where('competencies.id', $request->input('competency'));
            });
        }

        $courses = $query->latest('published_at')->paginate(9)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $competencies = Competency::orderBy('title')->get();
        $categories = Course::where('status', 'published')->distinct()->pluck('category');

        return view('public.courses.index', compact('courses', 'departments', 'competencies', 'categories'));
    }

    public function show(string $slug): View
    {
        $course = Course::with(['trainer.profile', 'department', 'modules.lessons', 'competencies', 'resources' => function ($q) {
            $q->where('is_private', false);
        }])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $relatedCourses = Course::where('status', 'published')
            ->where('id', '!=', $course->id)
            ->where(function ($q) use ($course) {
                $q->where('category', $course->category)
                    ->orWhere('department_id', $course->department_id);
            })
            ->take(3)
            ->get();

        return view('public.courses.show', compact('course', 'relatedCourses'));
    }
}
