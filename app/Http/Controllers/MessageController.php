<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        // Get all users except the authenticated user
        $users = User::where('id', '!=', auth()->user()->id)->get();

        // Initialize chatUser and messages
        $chatUser = null;
        $messages = [];

        // Check if there is a selected user
        if(request()->has('user')) {
            // Find the user based on the provided ID
            $chatUser = User::findOrFail(request('user'));

            // Example: Retrieve messages where the authenticated user is either the sender or recipient
            $messages = Message::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('recipient_id', request('user'));
            })->orWhere(function ($query) {
                $query->where('user_id', request('user'))
                    ->where('recipient_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();
        }

        return view('viewMessages', compact('users', 'chatUser', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required',
        ]);
    
        $message = new Message([
            'user_id' => auth()->user()->id,
            'content' => $request->content,
        ]);
    
        // Set 'recipient_id' only if it's available in the request
        if ($request->has('recipient_id')) {
            $message->recipient_id = $request->recipient_id;
        }
    
        $message->save();
    
        return redirect()->back();
    }
    



    public function sentMessages()
    {
        $sentMessages = auth()->user()->sentMessages; // Assuming you have a relationship set up for sent messages
        $users = User::where('id', '!=', auth()->user()->id)->get();

        return view('viewMessages', compact('sentMessages', 'users'));
    }

    public function viewMessages($userId)
    {
        // Get the list of users for the contacts sidebar
        $users = User::where('id', '!=', auth()->user()->id)->get();
        $sentMessages = auth()->user()->sentMessages;

        // Add logic to get the selected chat user and messages based on $userId
        // Inside the viewMessages method
        return view('viewMessages', compact('sentMessages', 'users'));

    }

    public function show(Request $request, $userId)
{
    // Assuming you want to get the list of users for the contacts sidebar
    $users = User::where('id', '!=', auth()->user()->id)->get();
    $sentMessages = auth()->user()->sentMessages;

    // Add logic to get the selected chat user and messages based on $userId
    $chatUser = User::find($userId);

    if (!$chatUser) {
        return abort(404); // Or any other way you want to handle the case where the user is not found
    }

    // Example: Retrieve messages where the authenticated user is either the sender or recipient
    $messages = Message::where(function ($query) use ($userId) {
        $query->where('user_id', auth()->id())
            ->where('recipient_id', $userId);
    })->orWhere(function ($query) use ($userId) {
        $query->where('user_id', $userId)
            ->where('recipient_id', auth()->id());
    })->orderBy('created_at', 'asc')->get();

    // Pass the data to the view
    return view('viewMessages', compact('chatUser', 'messages', 'users'));
}



    // Tambahkan fungsi lain sesuai kebutuhan (show, update, destroy)
}
