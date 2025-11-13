<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::all();
        return view('settings.channels.index', compact('channels'));
    }

    public function create()
    {
        return view('settings.channels.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:settings_channels']);
        Channel::create($request->all());
        return redirect()->route('settings.channels.index')->with('success', 'Canal criado com sucesso.');
    }

    public function edit(Channel $channel)
    {
        return view('settings.channels.edit', compact('channel'));
    }

    public function update(Request $request, Channel $channel)
    {
        $request->validate(['name' => 'required|string|max:255|unique:settings_channels,name,'.$channel->id]);
        $channel->update($request->all());
        return redirect()->route('settings.channels.index')->with('success', 'Canal atualizado com sucesso.');
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();
        return redirect()->route('settings.channels.index')->with('success', 'Canal excluído com sucesso.');
    }
}