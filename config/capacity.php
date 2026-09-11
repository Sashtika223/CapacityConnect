<?php

return [

    'certificates' => [
        'expiring_soon_days' => 30,
        'trainee_lifetime' => true,
        'trainer_validity_years' => 2,
    ],

    'risk' => [
        'inactivity_days' => 90,
        'low_assessment_percentage' => 60,
        'high_gap' => 3,
        'medium_gap' => 1,
        'declining_attempts' => 3,
    ],

    'gamification' => [
        'enabled' => true,
        'points' => [
            'course_completed' => 50,
            'assessment_passed' => 25,
            'high_score' => 15,
            'practical_passed' => 40,
            'lesson_completed' => 5,
            'recommended_training_completed' => 20,
        ],
        'level_size' => 100,
    ],

    'ai' => [
        'escalate_when_uncertain' => true,
    ],

];
