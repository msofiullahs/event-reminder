<?php

namespace App\Http\Controllers;

use App\Imports\EventImport;
use App\Imports\ImportEvent;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Receiver;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $events = Event::orderBy('created_at', 'DESC');

        if ($request->has('search') && !empty($request->search)) {
            $word = $request->search;
            $events = $events->where(function($q) use($word) {
                $q->where('title', 'LIKE', '%'.$word.'%')
                ->orWhere('unique_id', 'LIKE', '%'.$word.'%');
            });
        }

        if ($request->has('startDate') && !empty($request->startDate)) {
            $startDate = $request->startDate;
            $events = $events->whereDate('event_date', '>=', $startDate);
        }

        if ($request->has('endDate') && !empty($request->endDate)) {
            $endDate = $request->endDate;
            $events = $events->whereDate('event_date', '<=', $endDate);
        }

        $events = $events->paginate(20);
        if (!empty($request->all())) {
            $events = $events->appends($request->all());
        }

        return Inertia::render('Event/Index', ['events' => $events]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $uniqueId = $this->uniqueId();

        $data = new Event();
        $data->unique_id = $uniqueId;
        $data->title = $request->title;
        $data->type = $request->type;
        $data->location = $request->location;
        $data->event_date = Carbon::parse($request->event_date)->format('Y-m-d H:i:s');
        $data->send_reminder_time = Carbon::parse($request->send_reminder_time)->format('Y-m-d H:i:s');

        if ($data->save()) {
            $rmvSpace = str_replace(' ', '', $request->receivers);
            $receiverArr = explode(',', $rmvSpace);
            $receivers = [];
            foreach ($receiverArr as $rcv) {
                $receivers[] = new Receiver([
                    'email'=> $rcv,
                    'sent_status'=> 'pending',
                ]);
            }
            $data->receivers()->saveMany($receivers);

            return back()->with('posted', $data);
        }
        return back()->with('posted', 'failed');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id)
    {
        $event = Event::with('receivers')->where('id', $id)->first();
        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        $data = Event::find($id);
        $data->title = $request->title;
        $data->type = $request->type;
        $data->location = $request->location;
        $data->event_date = Carbon::parse($request->event_date)->format('Y-m-d H:i:s');
        $data->send_reminder_time = Carbon::parse($request->send_reminder_time)->format('Y-m-d H:i:s');

        if ($data->save()) {
            $data->receivers()->delete();
            $rmvSpace = str_replace(' ', '', $request->receivers);
            $receiverArr = explode(',', $rmvSpace);
            $receivers = [];
            foreach ($receiverArr as $rcv) {
                $receivers[] = new Receiver([
                    'email'=> $rcv,
                    'sent_status'=> 'pending',
                ]);
            }
            $data->receivers()->saveMany($receivers);

            return back()->with('posted', $data);
        }
        return back()->with('posted', 'failed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        $event = Event::find($id);
        $event->receivers()->delete();
        $event->delete();
        return back();
    }

    /**
     * Import CSV file to storage.
     */
    public function importEvents(Request $request)
    {
        try {
            if ($request->file('file')->getClientOriginalExtension() == 'csv') {
                // Handle CSV file
                $data = array_map('str_getcsv', file($request->file('file')));
                // dd($data);
                $newArr = [];
                foreach ($data as $key => $val) {
                    if ($key > 0) {
                        $item = $val[0];
                        $separator = ',';
                        if (strpos($item, ';')) {
                            $separator = ';';
                        }
                        $columns = explode($separator, $item);
                        $uniqueId = $this->uniqueId();
                        $event = new Event();
                        $event->unique_id = $uniqueId;
                        $event->title = $columns[0];
                        $event->type = $columns[1];
                        $event->location = $columns[2];
                        $event->event_date = $columns[3];
                        $event->send_reminder_time = $columns[4];
                        $event->save();
                    }
                }
            } else {
                // Handle Excel file
                Excel::import(new EventImport, $request->file('file'));
            }
            return back()->with('posted', 'success');
        } catch (Exception $e) {
            Log::info($e);
            return back()->with('posted', 'failed');
        }
    }

    function uniqueId()
    {
        $uniqueId = 'EVT'.str_pad('1', 10, '0', STR_PAD_LEFT);
        $latest = Event::latest()->first();
        if (!empty($latest)) {
            $uniqueId = 'EVT'.str_pad($latest->id+1, 10, '0', STR_PAD_LEFT);
        }
        return $uniqueId;
    }
}
