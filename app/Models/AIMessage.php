<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIMessage extends Model
{
    protected $table = 'ai_messages';

    protected $fillable = ['ai_conversation_id', 'role', 'content', 'cited_resources', 'is_ai_generated'];

    protected $casts = [
        'cited_resources' => 'array',
        'is_ai_generated' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AIConversation::class, 'ai_conversation_id');
    }
}
