<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;

class KnowledgeGraphService
{
    /**
     * Builds nodes and links graph dataset representing the capacity network.
     */
    public function getGraphData(): array
    {
        $nodes = [];
        $links = [];

        // 1. Department Nodes
        foreach (Department::take(6)->get() as $dept) {
            $nodes[] = [
                'id' => 'dept_'.$dept->id,
                'name' => $dept->name,
                'type' => 'department',
                'color' => '#3b82f6',
                'size' => 20,
            ];
        }

        // 2. Competency Nodes
        foreach (Competency::with('department')->take(12)->get() as $comp) {
            $nodes[] = [
                'id' => 'comp_'.$comp->id,
                'name' => $comp->title,
                'type' => 'competency',
                'color' => '#10b981',
                'size' => 16,
            ];

            if ($comp->department_id) {
                $links[] = [
                    'source' => 'dept_'.$comp->department_id,
                    'target' => 'comp_'.$comp->id,
                    'label' => 'requires',
                ];
            }
        }

        // 3. Course Nodes
        foreach (Course::with('competencies')->where('status', 'published')->take(8)->get() as $course) {
            $nodes[] = [
                'id' => 'course_'.$course->id,
                'name' => $course->title,
                'type' => 'course',
                'color' => '#f59e0b',
                'size' => 14,
            ];

            foreach ($course->competencies as $cComp) {
                $links[] = [
                    'source' => 'course_'.$course->id,
                    'target' => 'comp_'.$cComp->id,
                    'label' => 'builds',
                ];
            }

            if ($course->trainer_id) {
                $links[] = [
                    'source' => 'user_'.$course->trainer_id,
                    'target' => 'course_'.$course->id,
                    'label' => 'taught_by',
                ];
            }
        }

        // 4. Key Trainers & Expert Users
        foreach (User::whereIn('role', ['trainer', 'admin'])->take(6)->get() as $usr) {
            $nodes[] = [
                'id' => 'user_'.$usr->id,
                'name' => $usr->name,
                'type' => 'trainer',
                'color' => '#8b5cf6',
                'size' => 18,
            ];
        }

        return [
            'nodes' => $nodes,
            'links' => $links,
        ];
    }
}
