<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Worker extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Sortable;
    
    public $sortable = [
        'id',
        'first_name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','first_name',
    ];

    public static function rules()
    {
        $cust_id = (Auth::user()->parent_id == NULL) ? Auth::id() : Auth::user()->parent_id;

        return [
            'worker_id'                         => 'required|max:20|min:3|unique:workers,worker_id,NULL,id,cust_id,'.$cust_id,
            'aadhar_card_number'                => 'nullable|max:20|min:3',
            'first_name'                        => 'required|max:100|min:3',
            'contact_number_1'                  => 'required|max:10|min:10',
            'salary'                            => 'nullable|integer|max:50000|min:1',
        ];
    }

    public static function final_rules()
    {
        return self::rules();
    }

    public static function api_rules()
    {
        $rules = self::rules();
        return $rules;
    }

    public static function update_rules($request)
    {
        return [
            'first_name'                    => 'required|max:100|min:3',
            'contact_number_1'              => 'required|max:10|min:10',
            'aadhar_card_number'            => 'nullable|max:20|min:3',
            'salary'                        => 'nullable|integer|max:50000|min:1',
        ];
    }

    public static function messages()
    {
        return [
            'worker_id.required' => 'Worker ID is required',
            'worker_id.unique' => 'Worker ID already taken',
        ];
    }

}
