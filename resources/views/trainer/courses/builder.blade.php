@extends('layouts.trainer')

@section('title', 'Curriculum Builder — ' . $course->title)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
    <!-- Top Bar Navigation & Actions -->
    <div style="background: #1e293b; padding: 24px; border-radius: 14px; border: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <a href="{{ route('trainer.courses.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600;">← Courses</a>
                <span style="color: #475569;">|</span>
                <span class="badge" style="background: rgba(59,130,246,0.15); color: #60a5fa; font-size: 11px;">Status: {{ strtoupper($course->status) }}</span>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #f8fafc; margin: 0;">Syllabus Builder: {{ $course->title }}</h1>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); color: #fff; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                Preview Student Page 👁
            </a>
            @if($course->status === 'draft')
                <form action="{{ route('trainer.courses.submit', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        Submit Course for Approval 🚀
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Syllabus Content Structure Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Modules & Lessons Stack -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Modules & Lessons List -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0 0 16px 0;">
                    📖 Modules & Lessons Structure
                </h3>

                @forelse($course->modules as $modIndex => $module)
                    <div style="background: #0f172a; border-radius: 12px; border: 1px solid #334155; overflow: hidden; margin-bottom: 20px;">
                        <div style="padding: 16px; background: #182234; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 4px 0;">
                                    Module {{ $modIndex + 1 }}: {{ $module->title }}
                                </h4>
                                <p style="font-size: 12px; color: #94a3b8; margin: 0;">{{ $module->description }}</p>
                            </div>
                        </div>

                        <!-- Lessons inside Module -->
                        <div style="padding: 16px; display: flex; flex-direction: column; gap: 10px;">
                            @forelse($module->lessons as $les)
                                <div style="display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 12px 16px; border-radius: 8px; border: 1px solid #334155;">
                                    <div>
                                        <div style="font-size: 14px; font-weight: 600; color: #f8fafc;">
                                            {{ $les->title }}
                                        </div>
                                        <div style="font-size: 11px; color: #64748b;">
                                            Type: {{ ucfirst($les->content_type) }} • Duration: {{ $les->duration_minutes }} min
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p style="font-size: 12px; color: #64748b; margin: 0;">No lessons added to this module yet.</p>
                            @endforelse

                            <!-- Form to Add Lesson -->
                            <details style="margin-top: 10px; background: #1e293b; padding: 12px; border-radius: 8px; border: 1px dashed #334155;">
                                <summary style="color: #38bdf8; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    + Add New Lesson to Module {{ $modIndex + 1 }}
                                </summary>
                                <form action="{{ route('trainer.lessons.store', ['course' => $course->id, 'module' => $module->id]) }}" method="POST" style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
                                    @csrf
                                    <input type="text" name="title" placeholder="Lesson Title" required style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                        <select name="content_type" style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                                            <option value="text">Text / Article</option>
                                            <option value="video">Video Lecture</option>
                                            <option value="document">Document / Manual</option>
                                        </select>
                                        <input type="number" name="duration_minutes" placeholder="Duration (min)" value="15" required style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                                    </div>
                                    <input type="url" name="video_url" placeholder="Video Embed URL (optional)" style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;">
                                    <textarea name="text_content" rows="4" placeholder="Lesson Text Body Content / Transcripts" style="background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 13px;"></textarea>
                                    <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 12px; width: fit-content;">
                                        Save Lesson
                                    </button>
                                </form>
                            </details>
                        </div>
                    </div>
                @empty
                    <p style="font-size: 13px; color: #64748b;">No modules created yet. Add your first module below.</p>
                @endforelse

                <!-- Add Module Form -->
                <form action="{{ route('trainer.modules.store', $course->id) }}" method="POST" style="background: #0f172a; padding: 18px; border-radius: 12px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 12px;">
                    @csrf
                    <h4 style="font-size: 14px; font-weight: 700; color: #f8fafc; margin: 0;">+ Create New Syllabus Module</h4>
                    <input type="text" name="title" placeholder="Module Title (e.g. Flood Operations & Command Structure)" required style="background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;">
                    <textarea name="description" rows="2" placeholder="Brief module overview description" style="background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 10px 14px; color: #fff; font-size: 14px;"></textarea>
                    <button type="submit" class="btn btn-primary" style="background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; width: fit-content;">
                        Add Module ➔
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Assessments & Practical Scenarios Studio -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- MCQ Assessment Authoring -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 14px 0;">
                    📝 MCQ Quizzes & Questions
                </h3>

                <form action="{{ route('trainer.assessments.store', $course->id) }}" method="POST" style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                    @csrf
                    <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">+ Create MCQ Quiz</div>
                    <input type="text" name="title" placeholder="Quiz Title (e.g. Flood Protocol Evaluation)" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                    <select name="type" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                        <option value="mcq">Module Practice Quiz</option>
                        <option value="final">Final Certification Assessment</option>
                    </select>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <input type="number" name="time_limit_minutes" placeholder="Time Limit (min)" value="30" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                        <input type="number" name="passing_percentage" placeholder="Pass Score %" value="75" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                    </div>
                    <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        Create Assessment
                    </button>
                </form>

                @foreach($course->assessments as $quiz)
                    <div style="background: #0f172a; padding: 12px; border-radius: 8px; border: 1px solid #334155; margin-bottom: 10px;">
                        <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">{{ $quiz->title }} ({{ $quiz->questions->count() }} Questions)</div>

                        <details style="margin-top: 8px;">
                            <summary style="font-size: 11px; color: #38bdf8; cursor: pointer; font-weight: 600;">+ Add Question to Quiz</summary>
                            <form action="{{ route('trainer.questions.store', ['course' => $course->id, 'assessment' => $quiz->id]) }}" method="POST" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                                @csrf
                                <input type="text" name="question_text" placeholder="Question prompt..." required style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 6px 10px; color: #fff; font-size: 12px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                                    <input type="number" step="0.5" name="marks" value="1" placeholder="Marks" style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                    <select name="difficulty" style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                        <option value="Medium">Medium</option>
                                        <option value="Easy">Easy</option>
                                        <option value="Hard">Hard</option>
                                    </select>
                                </div>
                                <input type="text" name="options[0]" placeholder="Option 1" required style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                <input type="text" name="options[1]" placeholder="Option 2" required style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                <input type="text" name="options[2]" placeholder="Option 3" style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                <input type="text" name="options[3]" placeholder="Option 4" style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #fff; font-size: 11px;">
                                <select name="correct_option_index" style="background: #1e293b; border: 1px solid #334155; border-radius: 4px; padding: 4px 8px; color: #34d399; font-size: 11px; font-weight: 700;">
                                    <option value="0">Option 1 is Correct</option>
                                    <option value="1">Option 2 is Correct</option>
                                    <option value="2">Option 3 is Correct</option>
                                    <option value="3">Option 4 is Correct</option>
                                </select>
                                <button type="submit" class="btn btn-secondary" style="background: #2563eb; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600;">Save Question</button>
                            </form>
                        </details>
                    </div>
                @endforeach
            </div>

            <!-- Practical Assessment Authoring -->
            <div class="card" style="background: #1e293b; border-radius: 14px; border: 1px solid #334155; padding: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 0 0 14px 0;">
                    🧪 Practical Field Assessments
                </h3>

                <form action="{{ route('trainer.practicals.store', $course->id) }}" method="POST" style="background: #0f172a; padding: 14px; border-radius: 10px; border: 1px solid #334155; display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <div style="font-size: 13px; font-weight: 700; color: #f8fafc;">+ Add Practical Scenario Evaluation</div>
                    <input type="text" name="title" placeholder="Evaluation Title (e.g. Field Shelter Plan)" required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;">
                    <textarea name="scenario_prompt" rows="3" placeholder="Scenario details for trainee to respond to..." required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;"></textarea>
                    <textarea name="evaluation_criteria" rows="2" placeholder="Grading criteria for instructor..." required style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 8px 12px; color: #fff; font-size: 12px;"></textarea>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <input type="number" name="max_score" value="100" placeholder="Max Score" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                        <input type="number" name="passing_score" value="70" placeholder="Pass Score" style="background: #1e293b; border: 1px solid #334155; border-radius: 6px; padding: 6px 10px; color: #fff; font-size: 12px;">
                    </div>
                    <button type="submit" class="btn btn-secondary" style="background: #334155; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                        Save Practical Assessment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
