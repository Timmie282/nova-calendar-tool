<?php

namespace Czemu\NovaCalendarTool\Http\Controllers;

use Czemu\NovaCalendarTool\Models\Event;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Tenant\Project;
use App\Models\Tenant\Estate;

class EventsController
{
    public function index(Request $request)
    {
        $events = Event::filter($request->query())
            ->get(['id', 'estate_id', 'project_id', 'title', 'start', 'end', 'description'])
            ->toJson();

        return response($events);
    }

    public function store(Request $request)
    {
        $validation = Event::getModel()->validate($request->input(), 'create');

        if ($validation->passes())
        {
            $event = Event::create($request->input());

            if ($event)
            {
                return response()->json([
                    'success' => true,
                    'event' => $event
                ]);
            }
        }

        return response()->json([
            'error' => true,
            'message' => $validation->errors()->first()
        ]);
    }

    public function update(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $validation = Event::getModel()->validate($request->input(), 'update');

        if ($validation->passes())
        {
            $event->update($request->input());

            return response()->json([
                'success' => true,
                'event' => $event
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => $validation->errors()->first()
        ]);
    }

    public function destroy(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        if ( ! is_null($event))
        {
            $event->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true]);
    }

    public function projects()
    {
    	$projects = Project::select('pro_id', 'name')
		    ->get()
		    ->toJson();

    	return response($projects);
    }

	public function estates()
	{
		$estate = Estate::select('est_id', 'address')
		                   ->get()
		                   ->toJson();

		return response($estate);
	}

	public function currentEvent($id)
	{
		$events = Event::select('est_id', 'project_id', 'description')
			->where('id', $id)
			->first();

		return response($events);
	}
}