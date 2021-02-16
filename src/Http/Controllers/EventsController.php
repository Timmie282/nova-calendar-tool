<?php

namespace Czemu\NovaCalendarTool\Http\Controllers;

use Czemu\NovaCalendarTool\Models\Event;
use Illuminate\Http\Request;
use App\Models\Tenant\Project;
use App\Models\Tenant\Estate;

class EventsController
{
    public function index(Request $request)
    {
        $events = Event::filter($request->query())
            ->get(['cal_id', 'est_id', 'project_id', 'title', 'start', 'end', 'description']);

//        $projects = Project::select('pro_id', 'name')
//	        ->get();
//
//        $estates = Estate::select('est_id', 'address')
//	        ->get();
//
//        $events[] = $projects;
//        $events[] = $estates;
        $events = json_encode($events);

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
}