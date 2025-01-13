<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    public function index() {
        $search = request('search');

        if ($search) {
            $events = Event::where([
                ['title', 'like', "%$search%"]
            ])->get();
        } else {
            $events = Event::all();
        }

        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create() {
        return view('events.create');
    }

    public function show($id) {
        $event = Event::findOrFail($id);

        $eventOwner = User::where('id', $event->user_id)->first()->toArray();
        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner]);
    }

    public function store(Request $request) {

        $event = new Event;

        $event->title       = $request->title;
        $event->city        = $request->city;
        $event->private     = $request->private;
        $event->description = $request->description;
        $event->items       = $request->items;
        $event->date        = $request->date;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $requestImage = $request->image;

            $extension = $requestImage->getClientOriginalExtension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . '.' . $extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $event->image = $imageName;
        }

        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    public function destroy($id) {
        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    public function dashboard() {

        $user = auth()->user();

        //$events = Event::where('user_id', $user->id)->get();
        $events = $user->events; //Como nas models já está setado as constraints da pra pegar todos dessa forma direto

        return view('events.dashboard', ['events' => $events]);
    }
}
