<?php

namespace App\Console\Commands;

use App\Mail\ReminderEmail;
use App\Models\Receiver;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for event reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        // query all pending receiver
        $receivers = Receiver::with(['event' => function($q) use ($now) {
            $q->where('send_reminder_time', '<=', $now);
        }])->where('sent_status', 'pending')->all();

        foreach ($receivers as $rcv) {
            try {
                $title = $rcv->event->title;
                $event = $rcv->event;
                Mail::to($rcv->email)->send(new ReminderEmail($title, $event));
                $receiver = Receiver::find($rcv->id);
                $receiver->sent_status = 'sent';
                $receiver->save();
                $this->info('Email sent successfully!');
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }
    }
}
