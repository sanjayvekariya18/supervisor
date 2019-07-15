<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class RangeWiseBonus extends Model
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
            'machine_id'                    => 'required|max:20|min:1',
        ];
    }

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine','machine_id','machine_id');
    }

}
