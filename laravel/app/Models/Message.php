<?php

namespace App\Models;

use App\Events\NewMsgSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'channel_id',
        'content',
        'file_name',
        'file_size',
        'file_type'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    
    protected $dispatchesEvents = [
        //passes $message
        'created' => NewMsgSent::class
    ];
}
    