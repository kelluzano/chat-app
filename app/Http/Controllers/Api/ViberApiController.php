<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Session;
use App\Models\ClientDetail;
use Illuminate\Http\Request;
use Facade\FlareClient\Http\Client;
use App\Http\Controllers\Controller;
use App\Models\Message;

class ViberApiController extends Controller
{
    public function webhook(Request $request)
    {
        $json = file_get_contents("php://input");
        $input = json_decode($json, true);
        $event = $input['event'];

        switch ($event) {
            case "message":
                $this->received($input);
                break;
        }

        Log::info($input);
    }

    public function save_client($clientData)
    {

        $data = ClientDetail::where('uniqueId', $clientData['uniqueId'])->first();

        if ($data) return;

        ClientDetail::create($clientData);
    }

    // public function save_session()

    public function received($data)
    {

        $clientId = $data['sender']['id'];
        $clientName = $data['sender']['name'];
        $clientAvatar = $data['sender']['avatar'];

        $messageType = $data['message']['type'];

        $content = "";
        if ($messageType == "text") {
            $content = $data['message']['text'];
        }

        if ($messageType == "picture") {
            $content = $data['message']['media'];
        }

        $clientData = [
            'uniqueId' => $clientId,
            'name'     => $clientName,
            'avatar'   => $clientAvatar,
            'source'   => 'viber',
        ];

        $hasActiveSession = Session::where('uniqueId', $clientId)
            ->whereNull('close_date')->latest()->first();

        if(!$hasActiveSession){

            $session = Session::create([
                'uniqueId' => $clientId,
                'channel_name' => 'viber',
            ]);

            $message = $session->messages()->create([
                'content' => $content,
                'direction' => 'in',
            ]);

            $client = $this->save_client($clientData);

        }else{

            Message::create([
                'session_id' => $hasActiveSession->id,
                'content' => $content,
                'direction' => 'in',
            ]);
        }

        //save client details


        //check if has active session else saved

    }
}
