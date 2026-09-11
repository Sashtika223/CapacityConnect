<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeGraphEdge extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'relation',
        'target_type',
        'target_id',
        'label',
    ];
}
