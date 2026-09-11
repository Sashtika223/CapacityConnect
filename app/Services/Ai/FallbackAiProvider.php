<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\Models\Course;
use App\Models\Resource;

class FallbackAiProvider implements AiProvider
{
    public function complete(string $systemPrompt, string $userMessage, array $history = []): string
    {
        $q = mb_strtolower($userMessage);

        $resources = $this->matchingResources($q);

        if (str_contains($q, 'cyclone') || str_contains($q, 'storm') || str_contains($q, 'forecast')) {
            return $this->withCitations(
                "Cyclone preparedness (platform summary, not an official weather bulletin):\n\n1. Monitor official meteorological colour-coded alerts.\n2. Secure loose objects and confirm shelter logistics before landfall.\n3. Escalate to your incident commander for operational orders.\n\nI cannot invent official warning numbers or unpublished SOPs. Confirm current alerts with your authorised meteorological source.",
                $resources
            );
        }

        if (str_contains($q, 'flood') || str_contains($q, 'water') || str_contains($q, 'river')) {
            return $this->withCitations(
                "Flood risk management (learning summary):\n\n1. Map inundation and high-ground rally points using course GIS materials.\n2. Position rescue boats and water purification upstream of vulnerable embankments.\n3. Treat this as study guidance — field orders come from your command structure.",
                $resources
            );
        }

        if (str_contains($q, 'certificate') || str_contains($q, 'certification') || str_contains($q, 'verify')) {
            return 'Certificates are issued only after course completion, a passing assessment score, and a passed practical assessment when one exists. Public verification uses `/certificate/verify/{verification_code}` and does not display private contact details.';
        }

        if (str_contains($q, 'competency') || str_contains($q, 'digital twin') || str_contains($q, 'skill')) {
            return 'Your Competency Digital Twin combines observed profile skills, training history, assessments, practical results, and certificates. Calculated scores and AI recommendations are labelled separately and are not facts.';
        }

        if (str_contains($q, 'assessment') || str_contains($q, 'mcq') || str_contains($q, 'exam')) {
            return $this->withCitations(
                'Assessment preparation: review lesson summaries, attempt practice questions in your course, and focus on passing criteria shown on the assessment page. I will not invent unpublished exam items.',
                $resources
            );
        }

        return $this->withCitations(
            'I can help with course concepts, learning resources, assessment preparation, and competency questions using material available on this platform. If the question needs an official SOP or unpublished policy, escalate it to your trainer or department lead.',
            $resources
        );
    }

    /**
     * @return list<string>
     */
    protected function matchingResources(string $query): array
    {
        $courses = Course::query()
            ->where('status', 'published')
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', '%'.$query.'%')
                    ->orWhere('short_description', 'like', '%'.$query.'%');
            })
            ->orderBy('title')
            ->limit(3)
            ->pluck('title')
            ->all();

        if ($courses !== []) {
            return $courses;
        }

        return Resource::query()
            ->where('is_private', false)
            ->orderBy('title')
            ->limit(3)
            ->pluck('title')
            ->all();
    }

    /**
     * @param  list<string>  $resources
     */
    protected function withCitations(string $answer, array $resources): string
    {
        if ($resources === []) {
            return $answer."\n\nNo matching published course title was found. Ask your trainer if you need an official document.";
        }

        return $answer."\n\nRelated platform resources: ".implode('; ', $resources);
    }
}
