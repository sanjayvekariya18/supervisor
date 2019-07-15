<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Machine extends Model
{
    use Sortable;
    
    public $incrementing = false;
    public $timestamps = false;

    public $sortable = [
        'machine_id',
        'machine_number',
        'machine_name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
    ];

    public static function admin_rules($request = [])
    {
        $cust_id = $request->input('cust_id');

        return [
            'machine_id'                        => 'required|max:20|min:1|unique_with:machines,machine_id',
            'cust_id'                           => 'required',
            'machine_number'                    => 'required|max:20|min:1|unique_with:machines,cust_id',
        ];
    }

    public static function admin_rules_js()
    {

        return [
            'machine_id'                        => 'required|max:20|min:1',
            'cust_id'                           => 'required',
            'machine_number'                    => 'required|max:20|min:1',
        ];
    }

    public static function admin_update_rules($request = [],$machine_id = null)
    {
        $machine_number = $request->input('machine_number');

        return [
            'machine_id'                        => 'required|max:20|min:1|unique_with:machines,machine_id,'.$machine_id,
            'cust_id'                           => 'required',
            'machine_number'                    => 'unique_with:machines,cust_id,'.$machine_number.'=machine_number,'.$machine_id,
        ];
    }

    public static function messages()
    {
        return [
            'machine_id.required' => 'Machine ID is required',
            'machine_id.unique_with' => 'Machine ID already taken',
            'machine_number.required' => 'Machine Number is required',
            'machine_number.unique' => 'Machine Number is taken by current customer',
            'machine_number.unique_with' => 'Machine Number is taken by current customer',
        ];
    }

    public function machine_group()
    {
        return $this->belongsTo('App\Models\MachineGroup','group_id');
    }

    public function worker()
    {
        return $this->belongsTo('App\Models\Worker');
    }

    public function day_worker()
    {
        return $this->belongsTo('App\Models\Worker','day_worker_id','id');
    }

    public function night_worker()
    {
        return $this->belongsTo('App\Models\Worker','night_worker_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','cust_id','id');
    }
}
