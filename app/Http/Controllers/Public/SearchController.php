<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Competency;
use App\Models\Course;
use App\Models\Resource;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $difficulty = $request->query('difficulty');
        $courses = $trainers = $skills = $competencies = $resources = $announcements = collect();

        if ($query !== '') {
            $courses = Course::where('status', 'published')
                ->when($category, fn ($builder) => $builder->where('category', $category))
                ->when($difficulty, fn ($builder) => $builder->where('difficulty', $difficulty))
                ->where(fn ($builder) => $builder->where('title', 'like', "%{$query}%")->orWhere('short_description', 'like', "%{$query}%"))
                ->with('trainer')->limit(10)->get();
            $trainers = User::where('role', 'trainer')->where('status', 'active')->where('name', 'like', "%{$query}%")->limit(10)->get();
            $skills = Skill::where('name', 'like', "%{$query}%")->limit(10)->get();
            $competencies = Competency::where('title', 'like', "%{$query}%")->limit(10)->get();
            $resources = Resource::where('is_private', false)->where(fn ($builder) => $builder->where('title', 'like', "%{$query}%")->orWhere('description', 'like', "%{$query}%"))->limit(10)->get();
            $announcements = Announcement::where('title', 'like', "%{$query}%")->latest('published_at')->limit(10)->get();
        }

        return view('public.search', compact('query', 'category', 'difficulty', 'courses', 'trainers', 'skills', 'competencies', 'resources', 'announcements'));
    }
}
