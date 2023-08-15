<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index(Request $request)
    {

        $keywords = $request->input('search');
        $query = Session::query()
            ->when($keywords, function ($query) use ($keywords) {
                $query->where('uniqueId', 'like', "%{$keywords}%");
            });

        $sessions = $query->paginate(6);

        if ($request->wantsJson()) {
            return response()->json($sessions);
        }

        return Inertia::render('Messages/Index', [
            'sessions' => $sessions
        ]);
    }
}
