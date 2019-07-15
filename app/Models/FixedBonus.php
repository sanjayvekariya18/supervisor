<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class FixedBonus extends Model
{
    use Sortable;
    
    public $sortable = [
        'machine_id',
        'min_stitches',
        'min_stitches_bonus',
        'after_min_per_stitches',
        'after_min_per_stitches_bonus',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
    ];

    public static function rules($request = [])
    {
        

        return [
            'machine_id'                    => 'required',
            'min_stitches'                  => 'required|max:10000000|min:0|integer',
            'min_stitches_bonus'            => 'required|between:0,5000.00',
            'after_min_per_stitches'        => 'required|max:10000000|min:0|integer',
            'after_min_per_stitches_bonus'  => 'required|between:0,5000.00',
        ];
    }

    public static function range_rules($request = [])
    {
        

        return [
            'machine_id'                    => 'required',
        ];
    }

    public static function messages()
    {
        return [
            'machine_id.required' => 'Please select at least one machine',
        ];
    }

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine','machine_id','machine_id');
    }

}
