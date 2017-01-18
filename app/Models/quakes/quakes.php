<?php

namespace App\Models\quakes;

use Illuminate\Database\Eloquent\Model;

class quakes extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'quakes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lng','lat', 'depth', 'mag', 'from', 'description', 'date'
    ];

}
