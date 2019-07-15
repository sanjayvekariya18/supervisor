<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;

class Machine12HourData extends Model
{
    // use Sortable;
    
    // public $incrementing = false;
    public $timestamps = false;

    // public $sortable = [
    //     'machine_number',
    //     'machine_name',
    // ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'machine_12_hour_data';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
    ];

    public function machine_group()
    {
        // return $this->belongsTo('App\Models\MachineGroup','group_id');
    }

    public function worker()
    {
        return $this->belongsTo('App\Models\Worker');
    }

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine','machine_id','machine_id');
    }
}
