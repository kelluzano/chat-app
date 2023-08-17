<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Models\Session;
use Illuminate\Http\Request;


class MessageController extends Controller
{
    public function index(Request $request)
    {

        $keywords = $request->input('search');
        $query = Session::query()
            ->select('id', 'uniqueId', 'created_at')
            ->groupBy('uniqueId')
            ->where(function ($query) use ($keywords){
                $query->orwhere('uniqueId', 'like', "%{$keywords}%")
                        ->orwhereHas('client', function ($subquery) use ($keywords){
                            $subquery->where('name','like', "%{$keywords}%");
                        });
            })
            ->with('client')
            ->orderby('created_at', 'desc');

        $sessions = $query->paginate(6)->withQueryString();
    
        if ($request->wantsJson()) {
            return response()->json($sessions);
        }

        return Inertia::render('Messages/Index', [
            'sessions' => $sessions
        ]);
    }

    public function getSelectedMessages($uniqueId, Request $request)
    {

        if ($request->wantsJson()) {
            // Retrive sessions ids
            $sessions = Session::where('uniqueId', $uniqueId)->pluck('id');
            $messages = Message::whereIn('session_id', $sessions)->with(['user' => function ($query){
                $query->select('id', 'name');
            }])
            ->paginate(5);
            return response()->json($messages);
        }
    }

    public function send(Request $request)
    {

        if ($request->wantsJson()) {
            $request->merge(['status' => 'sending']);
            $save = Message::create($request->all());
            $message = Message::with('user')->find($save->id);
            return response()->json($message);
        }
    }
}
