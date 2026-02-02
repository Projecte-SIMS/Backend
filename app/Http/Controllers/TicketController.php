<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    public function index()
    {
        return Ticket::with('messages')->orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $ticket = Ticket::create($data);
        return response($ticket, Response::HTTP_CREATED);
    }

    public function show(Ticket $ticket)
    {
        return $ticket->load('messages');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
            'vehicle_id' => ['sometimes', 'nullable', 'exists:vehicles,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $ticket->update($data);
        return $ticket;
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
