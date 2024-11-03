<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    /**
     * モデルに関連付けるテーブル
     *
     * @var string
     */
    protected $table = 'chat_messages';

    protected $guarded = [
        'id'
    ];

    public function createMessage($message)
    {
        return ChatMessage::create(['message' => $message]);
    }
}
