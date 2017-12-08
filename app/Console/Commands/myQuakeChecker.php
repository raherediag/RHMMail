<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\quakes\quakes as mQuake;

class myQuakeChecker extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myQuakeChecker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Earthquake Checker';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cenais = ['latest', 'one', 'two', 'three'];
        $last_quake = head(mQuake::where('from', 'cenais')->orderby('date', 'DESC')->take(1)->get()->toArray());

        if ($last_quake == false) {
            $last_quake['time'] = strtotime('-7 day');
        } else {
            $last_quake['time'] = strtotime($last_quake['date']);
        }
        $rows = [];
        $adv = [];
        foreach ($cenais as $query) {
            $this->info('Receive information from Cenais with ' . $query . ' parameter');
            $res = json_decode(\helper::apiCall('http://www.cenais.cu/lastquake/php/service.php', ['service' => $query], 'GET'), true);

            if (!is_array($res)) {
                continue;
            }
            
            $this->info('Processing information');
            foreach ($res as $quake) {
                if (\helper::getValue($quake, 'lon', '') == '' && \helper::getValue($quake, 'lat', '') == '') {
                    continue;
                }

                if (\helper::getValue($quake, 'timeUTC', '') != '') {
                    $quake['timeUTC'] = str_replace(['/', '_'], ['-', ' '], $quake['timeUTC']);
                    $quake['time'] = strtotime($quake['timeUTC']);
                } else {
                    $quake['time'] = strtotime('now');
                }

                if ($quake['time'] > $last_quake['time']) {
                    $rows[] = [
                        'lng' => \helper::getValue($quake, 'lon', '0.00'),
                        'lat' => \helper::getValue($quake, 'lat', '0.00'),
                        'depth' => \helper::getValue($quake, 'z', '0'),
                        'mag' => \helper::getValue($quake, 'magnitud', '0'),
                        'from' => \helper::getValue($quake, 'from', 'cenais'),
                        'description' => \helper::getValue($quake, 'desc', ''),
                        'date' => \helper::getValue($quake, 'timeUTC', date('Y-m-d h:i:s')),
                        'created_at' => date('Y-m-d h:i:s'),
                        'updated_at' => date('Y-m-d h:i:s')
                    ];
                    if (\helper::getValue($quake, 'magnitud', '0') > 3 && (\helper::getValue($quake, 'lat', '0.00') > 18.6 && \helper::getValue($quake, 'lat', '0.00') < 21) && (\helper::getValue($quake, 'lon', '0.00') > -78.5 && \helper::getValue($quake, 'lon', '0.00') < -73)) {
                        $adv[] = [
                            'lng' => \helper::getValue($quake, 'lon', '0.00'),
                            'lat' => \helper::getValue($quake, 'lat', '0.00'),
                            'depth' => \helper::getValue($quake, 'z', '0'),
                            'mag' => \helper::getValue($quake, 'magnitud', '0'),
                            'from' => \helper::getValue($quake, 'from', 'cenais'),
                            'description' => \helper::getValue($quake, 'desc', ''),
                            'date' => \helper::getValue($quake, 'timeUTC', '0.00')
                        ];
                    }
                }
            }
        }
        if (count($rows) > 0) {
            $this->info('Saving information');
            mQuake::insert($rows);
        }

        if (count($adv) > 0) {
            \Mail::send('emails.quake_warning', ['contents' => ['quakes' => $adv]], function ($message) {
                $message->to('raherediag@gmail.com', 'Roberto A. Heredia');
                $message->subject('Quake warning');
            });
            $this->info('Email was sent.');
        }
    }
}
