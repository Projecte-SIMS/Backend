<?php

namespace App\Http\Controllers;

use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'ticket_id' => ['required', 'exists:tickets,id'],
            'user_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string'],
        ]);

        $msg = TicketMessage::create($data);
        return response($msg, Response::HTTP_CREATED);
    }

    public function destroy(TicketMessage $message)
    {
        $message->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
