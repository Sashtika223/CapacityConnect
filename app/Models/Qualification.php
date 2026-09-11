<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'degree',
        'institution',
        'field_of_study',
        'graduation_year',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
