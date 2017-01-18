<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

use App\Models\quakes\quakes as mQuake;

Route::get('/', function () {
    $cenais = ['latest', 'one', 'two', 'three'];
    $last_quake = head(mQuake::where('from', 'cenais')->orderby('time_utc', 'ASC')->take(1)->get()->toArray());

    if ($last_quake == false) {
        $last_quake['time'] = strtotime('-7 day');
    } else {
        $last_quake['time'] = strtotime($last_quake['time_utc']);
    }
    $rows = [];
    $adv = [];
    foreach ($cenais as $query) {
//        $this->info('Receive information from Cenais with ' . $query . ' parameter');
        $res = json_decode(\helper::apiCall('http://www.cenais.cu/lastquake/php/service.php', ['service' => $query], 'GET'), true);

//        $this->info('Processing information');
        foreach ($res as $quake) {
            if(\helper::getValue($quake, 'lon', '') == '' && \helper::getValue($quake, 'lat', '') == ''){
                continue;
            }
            
            if(\helper::getValue($quake, 'timeUTC', '') != ''){
                $quake['timeUTC'] = str_replace(['/', '_'], ['-', ' '], $quake['timeUTC']);
                $quake['time'] = strtotime($quake['timeUTC']);
            }else{
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
                    'date' => \helper::getValue($quake, 'timeUTC', date('Y-m-d h:i:s'))
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
//    return \Response::json(\App\Models\quakes\quakes::where('from','cenais')->orderby('time_utc','ASC')->take(1)->get()->toArray(),200,[]);
    return \Response::json($adv, 200, []);
//    return view('welcome');
});
