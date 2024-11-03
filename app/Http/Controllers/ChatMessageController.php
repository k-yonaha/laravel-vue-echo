<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index()
    {
        $chatMessages = ChatMessage::orderBy('id', 'desc')->get();
        return $chatMessages;
    }

    public function create(Request $request)
    {
        $chatMessage = new ChatMessage();
        $result = $chatMessage->createMessage($request->message);

        return $result->id;
    }
}
