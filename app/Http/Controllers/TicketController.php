<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->can('can.view.all.tickets')) {
            return Ticket::with(['user', 'messages'])->orderBy('created_at', 'desc')->get();
        }

        if ($user->can('can.view.own.tickets')) {
            return Ticket::where('user_id', $user->id)
                ->with('messages')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);

        $data = $request->validate([
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ]);

        $ticket = $request->user()->tickets()->create($data);

        return response($ticket, Response::HTTP_CREATED);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return $ticket->load(['messages.user', 'user']);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
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
        if (!Auth::user()->can('can.delete.any.ticket')) { 
             return response()->json(['message' => 'Only admins can delete'], 403);
        }

        $ticket->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}