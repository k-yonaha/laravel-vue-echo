<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\SendChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{

    /**
     * メッセージ一覧取得
     * 
     * @param 
     * @return Collection
     */
    public function index()
    {
        $chatMessages = ChatMessage::orderBy('id', 'asc')->get();
        return $chatMessages;
    }

    /**
     * メッセージの登録処理
     * 
     * @param Illuminate\Http\Request $request
     * @return integer
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $chatMessage = new ChatMessage();
        $result = $chatMessage->createMessage($user->id, $request->message);
        event(new SendChatMessage($result));

        return $result->id;
    }
}
