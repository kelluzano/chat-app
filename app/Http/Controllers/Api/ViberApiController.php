<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Session;
use App\Models\ClientDetail;
use Illuminate\Http\Request;
use Facade\FlareClient\Http\Client;
use App\Http\Controllers\Controller;
use App\Models\Message;
use DB;
use Illuminate\Support\Facades\Http;

class ViberApiController extends Controller
{

    const send_endpoint = "https://chatapi.viber.com/pa/send_message";

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

        // Log::info($input);
    }

    public function save_client($clientData)
    {

        $data = ClientDetail::where('uniqueId', $clientData['uniqueId'])->first();

        if ($data) return;

        ClientDetail::create($clientData);

        return "success";
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

        if (!$hasActiveSession) {

            DB::transaction(function () use ($clientId, $content, $clientData) {
                $session = Session::create([
                    'uniqueId' => $clientId,
                    'channel_name' => 'viber',
                ]);

                $message = $session->messages()->create([
                    'content' => $content,
                    'direction' => 'in',
                ]);

                $client = $this->save_client($clientData);

            });

        } else {

            Message::create([
                'session_id' => $hasActiveSession->id,
                'content' => $content,
                'direction' => 'in',
            ]);
        }
    }

    public function send($data){

        $authToken = env("VIBER_API_TOKEN");
        $receiver = $data['receiver'];
        $text = $data['text'];

        $data = [
            'auth_token' => $authToken,
            'receiver'   => $receiver,
            'type'       => 'text',
            'text'       => $text,
        ];

        $response = Http::post(self::send_endpoint, $data);

        if ($response->successful()) {
            return $response->json();
        } else {
            $errorMessage = $response->json()['error_message'];
            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
