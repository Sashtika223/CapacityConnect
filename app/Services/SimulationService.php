<?php

namespace App\Services;

use App\Models\SimulationAttempt;
use App\Models\SimulationScenario;
use App\Models\User;

class SimulationService
{
    /**
     * Start or resume a simulation attempt.
     */
    public function startAttempt(User $user, SimulationScenario $scenario): SimulationAttempt
    {
        // Check existing incomplete attempt
        $existing = SimulationAttempt::where('user_id', $user->id)
            ->where('scenario_id', $scenario->id)
            ->whereNull('completed_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        return SimulationAttempt::create([
            'user_id' => $user->id,
            'scenario_id' => $scenario->id,
            'current_node' => 'node_1',
            'path_taken' => [],
            'score' => 0,
            'passed' => false,
            'started_at' => now(),
        ]);
    }

    /**
     * Process user decision step in the simulation.
     */
    public function submitDecision(SimulationAttempt $attempt, string $choiceId): array
    {
        $scenario = $attempt->scenario;
        $treeData = $scenario->decision_tree_data ?? [];
        $currentNodeKey = $attempt->current_node;
        $currentNode = $treeData[$currentNodeKey] ?? null;

        if (! $currentNode) {
            return ['error' => 'Invalid simulation state'];
        }

        $choices = $currentNode['choices'] ?? [];
        $chosenOption = collect($choices)->firstWhere('id', $choiceId);

        if (! $chosenOption) {
            return ['error' => 'Selected decision option not found'];
        }

        $pointsAwarded = (int) ($chosenOption['points'] ?? 0);
        $consequence = $chosenOption['consequence'] ?? 'Decision recorded.';
        $nextNode = $chosenOption['next_node'] ?? 'end';

        $path = $attempt->path_taken ?? [];
        $path[] = [
            'node_id' => $currentNodeKey,
            'node_title' => $currentNode['title'] ?? 'Situation',
            'choice_id' => $choiceId,
            'choice_text' => $chosenOption['text'] ?? '',
            'points' => $pointsAwarded,
            'consequence' => $consequence,
            'timestamp' => now()->toIso8601String(),
        ];

        $newScore = $attempt->score + $pointsAwarded;
        $isFinished = ($nextNode === 'end' || ! isset($treeData[$nextNode]));

        $attempt->update([
            'current_node' => $nextNode,
            'path_taken' => $path,
            'score' => $newScore,
            'completed_at' => $isFinished ? now() : null,
            'passed' => $isFinished ? ($newScore >= $scenario->passing_score) : false,
            'final_feedback' => $isFinished ? ($newScore >= $scenario->passing_score
                ? 'Simulation Passed: Excellent tactical decision making and adherence to emergency safety standards.'
                : 'Simulation Failed: Decision path led to unacceptable casualties or logistics failure. Review SOPs and retry.') : null,
        ]);

        return [
            'consequence' => $consequence,
            'points_awarded' => $pointsAwarded,
            'total_score' => $newScore,
            'is_finished' => $isFinished,
            'next_node' => $isFinished ? null : ($treeData[$nextNode] ?? null),
            'passed' => $attempt->passed,
            'feedback' => $attempt->final_feedback,
        ];
    }
}
