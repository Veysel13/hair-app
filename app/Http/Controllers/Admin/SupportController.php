<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Business;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    private function business(): ?Business
    {
        $user = Auth::user();
        return $user->isAdmin() ? Business::first() : $user->businesses()->first();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $business = $this->business();

        // Admin ise tüm işletmelerin taleplerini görür, salon sahibi ise sadece kendi işletmesini görür
        $query = SupportTicket::with(['business', 'user', 'messages.user']);
        if (!$user->isAdmin() && $business) {
            $query->where('business_id', $business->id);
        }

        $tickets = $query->orderByDesc('last_message_at')->get();

        $selectedTicketId = $request->get('ticket');
        $activeTicket = $selectedTicketId 
            ? $tickets->firstWhere('id', $selectedTicketId) 
            : $tickets->first();

        // Bu sayfaya girildiğinde bildirimleri oku
        AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('admin.support.index', compact('tickets', 'activeTicket', 'business'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $business = $this->business();

        $data = $request->validate([
            'subject'  => 'required|string|max:255',
            'category' => 'required|in:question,suggestion,technical,billing',
            'message'  => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'business_id'     => $business ? $business->id : Business::first()->id,
            'user_id'         => $user->id,
            'subject'         => $data['subject'],
            'category'        => $data['category'],
            'status'          => 'open',
            'last_message_at' => now(),
        ]);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'is_admin_reply'    => $user->isAdmin(),
            'message'           => $data['message'],
        ]);

        return redirect()->route('admin.support.index', ['ticket' => $ticket->id])
            ->with('success', 'Destek talebiniz ve mesajınız başarıyla iletildi. En kısa sürede yanıtlanacaktır.');
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();
        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $isAdminReply = $user->isAdmin();

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id'           => $user->id,
            'is_admin_reply'    => $isAdminReply,
            'message'           => $data['message'],
        ]);

        $ticket->update([
            'status'          => $isAdminReply ? 'replied' : 'open',
            'last_message_at' => now(),
        ]);

        // Admin yanıt verdiyse salon sahibine anlık bildirim at
        if ($isAdminReply && $ticket->user_id !== $user->id) {
            AppNotification::create([
                'user_id' => $ticket->user_id,
                'title'   => '💬 Salonim Destek Ekibi Yanıt Verdi',
                'message' => '"' . $ticket->subject . '" konulu destek talebinize yeni bir yanıt geldi.',
                'link'    => route('admin.support.index', ['ticket' => $ticket->id]),
                'is_read' => false,
            ]);
        }

        return redirect()->route('admin.support.index', ['ticket' => $ticket->id])
            ->with('success', 'Mesajınız iletildi.');
    }

    public function markNotificationsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }
}
