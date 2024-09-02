<?php

namespace App\Imports;

use App\Models\Event;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class EventImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $uniqueId = 'EVT'.str_pad('1', 10, '0', STR_PAD_LEFT);
        $latest = Event::latest()->first();
        if (!empty($latest)) {
            $uniqueId = 'EVT'.str_pad($latest->id+1, 10, '0', STR_PAD_LEFT);
        }
        return new Event([
            'unique_id'         => $uniqueId,
            'title'             => $row['title'],
            'type'              => $row['type'],
            'location'          => $row['location'],
            'event_date'        => Carbon::parse($row['event_date'])->format('Y-m-d H:i:s'),
            'send_reminder_time'=> Carbon::parse($row['send_reminder_time'])->format('Y-m-d H:i:s'),
        ]);
    }
}
