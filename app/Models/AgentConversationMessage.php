<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentConversationMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'message',
        'id',
        'user_id',
        'content',
        'attachment',
        'tool_calls',
        'tool_results',
        'usage',
        'meta',
        'created_at',
    ];
    public $incrementing = false;

    public function conversation(): BelongsTo
    {
        return $this-> belongsTo(AgentConversation::class);
    }
}
