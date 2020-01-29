<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mySendMail extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mySendMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Mails To ComercioOnline and Las-Avispas';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Mail::send('emails.sellTemplates.reello', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->to('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Nuevo servicio para casas de alquiler.');
        });
        \Log::info('Email Reello was sent.');
        
        $this->info('Email was sent.');
    }
}
