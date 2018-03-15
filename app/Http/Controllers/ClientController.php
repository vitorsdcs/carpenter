<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    public function show(Request $request, $id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function store(Request $request)
    {
        $this->validate($request, Client::$rules);

        $client = Client::create($request->all());

        return response()->json($client, 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, Client::$rules);

        $client = Client::find($id);
        $client->fill($request->all());
        $client->save();

        return response()->json($client);
    }

    public function destroy(Request $request, $id)
    {
        $client = Client::find($id);
        $client->delete();
        return response()->json(null, 204);
    }
}