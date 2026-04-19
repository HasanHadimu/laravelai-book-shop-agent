<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentConversation extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'title'
    ];
    public $incrementing = false;

    public function conversation(): HasMany
    {
        return $this-> hasMany(AgentConversation::class);
    }
}
