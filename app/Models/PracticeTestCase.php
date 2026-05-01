<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeTestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_id',
        'name',
        'type',
        'weight',
        'script',
        'timeout_ms',
        'sort_order',
        'is_required',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:1',
            'timeout_ms' => 'integer',
            'sort_order' => 'integer',
            'is_required' => 'boolean',
            'script' => 'array',
        ];
    }

    public const TYPES = [
        'dom',
        'css',
        'behavior',
        'console_errors',
        'snapshot',
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class, 'practice_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PracticeTestResult::class, 'practice_test_case_id');
    }
}