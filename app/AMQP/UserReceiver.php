<?php

namespace App\AMQP;

use App\User;

class UserReceiver
{
    public function handle($message)
    {
        $data = json_decode($message->body);
        list($entity, $client_id, $operation) = explode('.', $message->delivery_info['routing_key']);

        switch ($operation) {
            case 'created':
            case 'updated':
                $user = User::firstOrNew(['external_id' => (int) $data->id, 'client_id' => $client_id]);
                $user->external_id = (int) $data->id;
                $user->client_id = $client_id;
                $user->name = $data->name;
                $user->email = $data->email;
                $user->save();
                break;
            case 'deleted':
                $user = User::where(['external_id' => (int) $data->id, 'client_id' => $client_id])->firstOrFail();
                $user->delete();
                break;
        }
    }
}
