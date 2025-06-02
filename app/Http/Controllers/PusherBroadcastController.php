<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PusherBroadcastController extends Controller
{
    public function index(Job $job)
    {
        $messages = Message::where('job_id', $job->id)
            ->with(['sender', 'receiver'])
            ->orderBy('sent_at', 'asc')
            ->get();

        return view('messages.index', compact('job', 'messages'));
    }

    public function broadcast(Request $request, Job $job)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id'
        ]);

        $message = Message::create([
            'job_id' => $job->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false
        ]);

        $message->load(['sender', 'receiver']);

        broadcast(new \App\Events\PusherBroadcast($message))->toOthers();

        return response()->json([
            'message' => $message
        ]);
    }

    public function receive(Request $request, Job $job)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = Message::create([
            'job_id' => $job->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $job->client_id === Auth::id() ? $job->contractor_id : $job->client_id,
            'message' => $request->message,
            'is_read' => false
        ]);

        $message->load(['sender', 'receiver']);

        return response()->json([
            'message' => $message
        ]);
    }
}
