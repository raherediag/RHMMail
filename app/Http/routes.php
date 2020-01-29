<?php

Route::get('/', function () {
    $processes = [];
    
    if(\Cache::has('processes')){
        $processes = json_decode(\Cache::get('processes'),true);
    }
    
    return \Response::json($processes,200,[]);
//    return view('welcome');
});

Route::get('/test/{name}/{from}/{to}', function ($name,$from,$to) {
    ob_end_clean();
    header("Connection: close");
    ignore_user_abort(); // optional
    ob_start();
    echo ('Text the user will see');
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush(); // Strange behaviour, will not work
    flush();            // Unless both are called !
    Artisan::call('test:for', ['name' => $name, 'from' => $from, 'to' => $to]);
});
