<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\PracticalAssessment;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TrainerCurriculumController extends Controller
{
    public function builder(Course $course): View
    {
        $this->authorize('update', $course);

        $course->load(['modules.lessons.resources', 'modules.assessments.questions.options', 'assessments.questions.options', 'practicalAssessments']);

        return view('trainer.courses.builder', compact('course'));
    }

    public function storeModule(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $course->modules()->max('sort_order') ?? 0;

        CourseModule::create([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Module created.');
    }

    public function storeLesson(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'content_type' => 'required|in:text,video,document',
            'text_content' => 'nullable|string',
            'video_url' => 'nullable|string|url',
            'duration_minutes' => 'required|integer|min:1',
            'is_free_preview' => 'nullable|boolean',
        ]);

        $maxOrder = $module->lessons()->max('sort_order') ?? 0;

        Lesson::create([
            'module_id' => $module->id,
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')).'-'.Str::random(4),
            'content_type' => $request->input('content_type'),
            'text_content' => $request->input('text_content'),
            'video_url' => $request->input('video_url'),
            'duration_minutes' => $request->input('duration_minutes'),
            'sort_order' => $maxOrder + 1,
            'is_free_preview' => $request->boolean('is_free_preview'),
        ]);

        return back()->with('success', 'Lesson added to module.');
    }

    public function storeResource(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'module_id' => 'nullable|exists:course_modules,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'file' => 'required|file|max:20480',
            'is_private' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('course_resources');

        Resource::create([
            'course_id' => $course->id,
            'module_id' => $request->input('module_id'),
            'lesson_id' => $request->input('lesson_id'),
            'uploader_id' => Auth::id(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'is_private' => $request->boolean('is_private', true),
        ]);

        return back()->with('success', 'Learning resource uploaded securely.');
    }

    public function storeAssessment(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:mcq,final',
            'time_limit_minutes' => 'required|integer|min:5|max:180',
            'passing_percentage' => 'required|integer|min:40|max:100',
            'negative_marks_per_question' => 'nullable|numeric|min:0|max:5',
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'time_limit_minutes' => $request->input('time_limit_minutes'),
            'passing_percentage' => $request->input('passing_percentage'),
            'negative_marks_per_question' => $request->input('negative_marks_per_question', 0.0),
            'is_randomized' => true,
            'is_active' => true,
        ]);

        return back()->with('success', 'Assessment created! You can now add questions.');
    }

    public function storeQuestion(Request $request, Course $course, Assessment $assessment): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'marks' => 'required|numeric|min:0.5',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option_index' => 'required|integer',
        ]);

        $maxSort = $assessment->questions()->max('sort_order') ?? 0;

        $question = Question::create([
            'assessment_id' => $assessment->id,
            'question_text' => $request->input('question_text'),
            'explanation' => $request->input('explanation'),
            'marks' => $request->input('marks'),
            'difficulty' => $request->input('difficulty'),
            'sort_order' => $maxSort + 1,
        ]);

        $correctIndex = (int) $request->input('correct_option_index');

        foreach ($request->input('options') as $index => $optionText) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index === $correctIndex),
                'sort_order' => $index + 1,
            ]);
        }

        return back()->with('success', 'Question and options saved.');
    }

    public function storePractical(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'scenario_prompt' => 'required|string|min:30',
            'evaluation_criteria' => 'required|string|min:30',
            'max_score' => 'required|integer|min:10',
            'passing_score' => 'required|integer|min:5',
        ]);

        PracticalAssessment::create([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'scenario_prompt' => $request->input('scenario_prompt'),
            'evaluation_criteria' => $request->input('evaluation_criteria'),
            'max_score' => $request->input('max_score'),
            'passing_score' => $request->input('passing_score'),
        ]);

        return back()->with('success', 'Practical scenario assessment added.');
    }
}
