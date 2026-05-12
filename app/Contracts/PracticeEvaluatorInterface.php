<?php

namespace App\Contracts;

use App\Models\PracticeSubmission;

interface PracticeEvaluatorInterface
{
    public function evaluate(PracticeSubmission $submission): array;
}
