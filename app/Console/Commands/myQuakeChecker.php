<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\quakes\quakes as mQuake;
use DateTime;
use DateTimeZone;

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
        echo "Start Earthquake Checker";
        \Log::info('Start Earthquake Checker');

        $this->getCenaisInfo();

        $this->getUSGSInfo();

        $this->sendEmail();

        echo "End Earthquake Checker";
        \Log::info('End Earthquake Checker');
    }

    private function getCenaisInfo()
    {
        $cenais = ['latest', 'one', 'two', 'three'];
        $last_quake = head(mQuake::where('provider', 'cenais')->orderby('date', 'DESC')->take(1)->get()->toArray());

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
                        'provider' => \helper::getValue($quake, 'provider', 'cenais'),
                        'description' => \helper::getValue($quake, 'desc', ''),
                        'date' => \helper::getValue($quake, 'timeUTC', date('Y-m-d H:i:s')),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }
        if (count($rows) > 0) {
            $this->info('Saving information');
            mQuake::insert($rows);
        }

    }

    private function getUSGSInfo()
    {
        $usgs = ['hour', 'day', 'week'];
        $last_quake = head(mQuake::where('provider', 'usgs')->orderby('date', 'DESC')->take(1)->get()->toArray());

        if ($last_quake == false) {
            $last_quake['time'] = strtotime('-7 day');
        } else {
            $last_quake['time'] = strtotime($last_quake['date']);
        }
        $rows = [];
        $now = date('Y-m-d H:i:s');
        foreach ($usgs as $query) {
            $this->info('Receive information USGS provider of the last ' . $query . ' parameter');
            $res = json_decode(\helper::apiCall('https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/2.5_' .  $query . '.geojson', [], 'GET'), true);

            if (!is_array($res)) {
                continue;
            }
            
            $this->info('Processing information');
            $features = $res['features'];
            foreach ($features as $quake) {
                if (\helper::getValue($quake, 'type', '') != 'Feature') {
                    continue;
                }

                if (\helper::getValue($quake, 'timeUTC', '') != '') {
                    $quake['timeUTC'] = str_replace(['/', '_'], ['-', ' '], $quake['timeUTC']);
                    $quake['time'] = strtotime($quake['timeUTC']);
                } else {
                    $quake['time'] = strtotime('now');
                }

                if ($quake['time'] > $last_quake['time']) {
                    $time = date('c', \helper::getValue($quake, 'properties.time', microtime())/1000);
                    $_id = \helper::getValue($quake, 'id', null);
                    $_quake = mQuake::where([
                        ['provider', 'usgs'],
                        ['provider_id', $_id],
                    ])->first();
                    $date = new DateTime($time);
                    $date->setTimezone(new DateTimeZone("UTC"));
                    if($_quake){
                        $_quake->lng = \helper::getValue($quake, 'geometry.coordinates.0', '0.00');
                        $_quake->lat = \helper::getValue($quake, 'geometry.coordinates.1', '0.00');
                        $_quake->depth = \helper::getValue($quake, 'geometry.coordinates.2', '0.00');
                        $_quake->mag = \helper::getValue($quake, 'properties.mag', '0');
                        $_quake->description = \helper::getValue($quake, 'properties.place', '0');
                        $_quake->date = $date->format("Y-m-d H:i:s");
                        $_quake->updated_at = $now;
                        $_quake->save();
                    }else{
                        if($_id){
                            $rows[$_id] = [
                                'lng' => \helper::getValue($quake, 'geometry.coordinates.0', '0.00'),
                                'lat' => \helper::getValue($quake, 'geometry.coordinates.1', '0.00'),
                                'depth' => \helper::getValue($quake, 'geometry.coordinates.2', '0'),
                                'mag' => \helper::getValue($quake, 'properties.mag', '0'),
                                'provider' => \helper::getValue($quake, 'provider', 'usgs'),
                                'provider_id' => $_id,
                                'description' => \helper::getValue($quake, 'properties.place', ''),
                                'date' => $date->format("Y-m-d H:i:s"),
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                        }
                    }
                }
            }
        }
        if (count($rows) > 0) {
            $this->info('Saving information');
            mQuake::insert($rows);
        }
        
    }

    private function sendEmail(){

        $features = mQuake::where([
            ['mag', '>', 3],
            ['lng', '>', -79],
            ['lng', '<', -72],
            ['lat', '>', 19],
            ['lat', '<', 21],
            ['sent', '=', 0],
        ])->orderBy('date','DESC')->get()->toArray();

        if (count($features) > 0) {
            \Mail::send('emails.quake_warning', ['contents' => ['quakes' => $features]], function ($message) {
                $message->to('raherediag@gmail.com', 'Roberto A. Heredia');
                $message->to('tfuentes@gmail.com', 'Teresita Fuentes');
                $message->subject('Quake warning');
            });
            $this->info('Email was sent.');

            foreach ($features as $quake) {
                $adv[] = $quake['id'];
            }

            $features = mQuake::whereIn('id',$adv)->update(['sent' => 1]);
        }
        \Log::info('End Earthquake Checker');
        echo "End Earthquake Checker";
    }
}
