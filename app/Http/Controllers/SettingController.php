<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::all();
        return response()->json($settings);
    }

    public function show(Request $request, $name)
    {
        $setting = Setting::findOrFail($name);
        return response()->json($setting);
    }

    public function store(Request $request)
    {
        $this->validate($request, Setting::$rules);

        $client = Client::getClient();
        $setting = $client->settings()->create($request->all());

        return response()->json($setting, 201);
    }

    public function update(Request $request, $name)
    {
        $this->validate($request, Setting::$rules);

        $setting = Setting::findOrFail($name);
        $setting->fill($request->all());
        $setting->save();

        return response()->json($setting);
    }

    public function destroy(Request $request, $name)
    {
        $setting = Setting::findOrFail($name);
        $setting->delete();
        return response()->json(null, 204);
    }
}
