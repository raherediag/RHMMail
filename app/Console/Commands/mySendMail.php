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
        \Mail::send('emails.sellTemplates.splitRoyal', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->cc('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Se vende Split ROYAL 1Tn 220v a 600 CUC en la Habana');
        });
        \Log::info('Email Split ROYAL was sent.');

        \Mail::send('emails.sellTemplates.lavadora', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->cc('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Se vende Lavadora LG 8.6 Kg a 500 CUC en la Habana en la Habana');
        });
        \Log::info('Email Lavadora LG 8.6 Kg was sent.');

        \Mail::send('emails.sellTemplates.cocina', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->cc('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Se vende Cocina ROYAL Gas 4Q con Horno a 280 CUC en la Habana');
        });
        \Log::info('Email Cocina ROYAL Gas was sent.');

        \Mail::send('emails.sellTemplates.lijadora', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->cc('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Se vende Lijadoras 220w STANLEY a 100 CUC en la Habana');
        });
        \Log::info('Email Lijadoras 220w STANLEY was sent.');
        
        \Mail::send('emails.sellTemplates.electrodomesticos', [], function ($message) {
            $message->to('comercioonline@googlegroups.com', 'comercioonline');
            $message->to('las-avispas@googlegroups.com', 'las-avispas');
            // $message->cc('raherediag@gmail.com', 'Roberto A. Heredia');
            $message->subject('Se venden Split, Lavadora, Cocina y Lijadoras en la Habana');
        });
        \Log::info('Email electrodomesticos was sent.');
        
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
