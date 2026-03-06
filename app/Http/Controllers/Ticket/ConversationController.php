<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class ConversationController extends VoyagerBaseController
{

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin')) {

            $tickets = Ticket::where('assigne_to', $user->id)
                ->orWhere('user_id', $user->id)
                ->with('messages', 'user', 'assigneTo')
                ->get();
        } else {
            $tickets = Ticket::with('messages', 'user', 'assigneTo')->get();
        }

        view()->share('tickets', $tickets);

        // Appel du parent index pour Voyager
        return parent::index($request);
    }

    public function openTicket(Request $request)
    {
        // ✅ Validation
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:255',
            'priority' => 'required|in:Basse,Moyenne,Haute',
            'assigne_to' => 'required|exists:users,id|not_in:' . Auth::id(),
            'user_id' => 'required|exists:users,id'
        ], [
            'assigne_to.not_in' => 'Vous ne pouvez pas vous assigner le ticket à vous-même.',
        ]);

        /* 
        'subject',
        'status',
        'priority',
        'assigne_to',
        'user_id',
        'num_ticket',
         */
        $ticket = Ticket::create([
            'subject' => $validated['subject'],
            'status' => 'Open',
            'priority' => $validated['priority'],
            'assigne_to' => $validated['assigne_to'],
            'user_id' => $validated['user_id'],
            'message' => $validated['message'],
            'order' => 1,
            'send_by' => Auth::id()
        ]);

        $currentYear = date('Y');

        $ticketId = $ticket->id;

        $ticketNumber = 'TK-' . $currentYear . '-' . $ticketId;

        $ticket->update(['num_ticket' => $ticketNumber]);

        //Conversation

        $conversation = Conversation::create([
            'ticket_id' => $ticketId,
            'message' => $validated['message'],
            'order' => 1,
            'send_by' => Auth::id()
        ]);


        return redirect()->route('voyager.tickets.index')
            ->with('success', 'Ticket créé avec succès !');
    }

    public function replyTicket(Request $request, $id)
    {

        $ticket = Ticket::where('id', $id)->with('messages', 'user', 'assigneTo')->first();

        return view('voyager::tickets.answer', compact('ticket', 'id'));
    }

    public function SendreplyTicket(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
            'id' => 'required|exists:tickets,id'
        ]);

        // Dernière conversation du ticket
        $lastConversation = Conversation::where('ticket_id', $validated['id'])
            ->orderByDesc('order')
            ->first();

        $responseTime = null;
        if ($lastConversation) {
            $responseTime = now()->diffInSeconds($lastConversation->created_at);
        }

        $nextOrder = $lastConversation ? $lastConversation->order + 1 : 1;

        Conversation::create([
            'ticket_id' => $validated['id'],
            'message' => $validated['message'],
            'order' => $nextOrder,
            'send_by' => Auth::id(),
            'response_time' => $responseTime
        ]);

        return redirect()->back()->with('success', 'Message envoyé avec succès !');
    }
}
