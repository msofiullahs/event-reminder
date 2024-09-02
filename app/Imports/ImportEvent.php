<?php

namespace App\Imports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportEvent implements ToModel
{
    /**
    * @param Array $row
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
            'title'             => $row[0],
            'type'              => $row[1],
            'location'          => $row[2],
            'event_date'        => $row[3],
            'send_reminder_time'=> $row[4],
        ]);
    }
}
