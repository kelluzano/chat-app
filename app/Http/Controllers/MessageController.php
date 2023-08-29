<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ViberApiController;
use Inertia\Inertia;
use App\Models\Message;
use App\Models\Session;
use Illuminate\Http\Request;
use Log;

class MessageController extends Controller
{
    public function index(Request $request)
    {

        $keywords = $request->input('search');
        $query = Session::query()
            ->where('assigned_to', auth()->user()->id)
            ->whereIn('id', function ($subquery) {
                $subquery->selectRaw('MAX(id)')
                ->from('sessions')
                ->groupBy('uniqueId');
            })
            ->where(function ($q) use ($keywords) {
                $q->orwhere('uniqueId', 'like', "%{$keywords}%")
                ->orwhereHas('client', function ($subquery) use ($keywords) {
                    $subquery->where('name', 'like', "%{$keywords}%");
                });
            })
            ->with(['client', 'lastMessageReceived', 'messageNotSeen'])
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
            $messages = Message::whereIn('session_id', $sessions)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }, 'session' => function ($query){
                $query->select('id', 'disposition', 'close_date', 'created_at');
            }])
            ->orderby('id')
            ->paginate(10);
            return response()->json($messages);
        }
    }

    public function send(Request $request)
    {

        if ($request->wantsJson()) {

            $session = Session::with('messages')->find($request->session_id);
            $status = "";

            if ($session->channel_name === "viber") {

                $data = [
                    'receiver' => $session->uniqueId,
                    'text'     => $request->content,
                ];

                $res = $this->send_to_viber($data);

                $status_message = $res['status_message'];
                if ($status_message === "ok") {
                    $status = "sent";
                } else {
                    $status = "failed";
                    Log::error(json_encode($res));
                }
            }

            $request->merge(['status' => $status]);
            $save = Message::create($request->all());
            $message = Message::with(['user'])->find($save->id);

            return response()->json($message);
        }
    }

    public function send_to_viber($data)
    {

        $api = new ViberApiController;
        $response = $api->send($data);

        return $response;
    }

    public function update_seen(Request $request){
       $message_ids = $request->ids;

       $update = Message::whereIn('id', $message_ids)->update([
            'seen' => 1,
       ]);

       return response()->json(['msg' => "success"]);
    }
}
