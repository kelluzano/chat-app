<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function list(){

        return Inertia::render();
    }

    public function end_session(Request $request){

        $date = date("Y-m-d H:i:s");
        $session = Session::find($request->session_id);
        $session->update(['close_date' => $date, 'updated_by' => auth()->user()->id]);

        $toast = [
            "title" => "Session",
            "body"  => "Successfully ended session id {$session->id}.",
            "class" => "bg-success"
        ];

        return response()->json(["toast" => $toast, "date" => $date]);
    }
}
