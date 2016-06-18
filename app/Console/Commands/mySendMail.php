<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mySendMail extends Command {

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
    public function handle() {

        \Mail::send('emails.sellhouse', [], function($message) {
            $message->to('raherediag@gmail.com', 'Roberto A. Heredia')->subject('Test');
            $message->subject('Test');
        });

        $this->info('Email was sent.');
    }

}
