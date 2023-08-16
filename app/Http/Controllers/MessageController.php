<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class MessageController extends Controller
{
    public function index(Request $request)
    {

        $keywords = $request->input('search');
        $query = Session::query()
            ->groupBy('uniqueId')
            ->with(['messages.user'])
            ->when($keywords, function ($query) use ($keywords) {
                $query->where('uniqueId', 'like', "%{$keywords}%");
            })
            ->orderby('created_at', 'desc');

        $sessions = $query->paginate(6);

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
            $uniqueId = "Ms. Matilda Lindgren"; // Your unique ID value

            // Retrieve the session with eager-loaded messages
            $session = Session::with('messages')->where('uniqueId', $uniqueId)->first();

            // Paginate the messages manually
            $perPage = 10; // Number of messages per page
            $page = request()->get('page', 1);

            $messages = $session->messages->forPage($page, $perPage);

            $paginator = new Paginator($messages, $perPage, $page);
            dd($paginator);
            // Render pagination links
            $paginationLinks = $paginator->render();
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
