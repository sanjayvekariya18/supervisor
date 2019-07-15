<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Auth;
class MachineGroup extends Model
{
    use Notifiable;
    use Sortable;
    
    public $sortable = [
        'group_name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','group_name',
    ];

    public static function rules()
    {
        return [
            'group_name'  => 'required|max:100|min:1',
        ];
    }

    public static function final_rules()
    {
        $cust_id = Auth::id();

        return [
            'group_name'    => 'required|max:100|min:1|unique:machine_groups,group_name,NULL,id,cust_id,'.$cust_id,
        ];
    }

    public static function update_rules($request)
    {
        $cust_id = Auth::id();
        $id = $request->id;

        return [
            'group_name'    => 'required|max:100|min:1|unique:machine_groups,group_name,'.$id.',id,cust_id,'.$cust_id,
        ];

    }

    public static function messages()
    {
        return [
            'group_name.required' => 'Group name is required',
            'group_name.unique' => 'Group name already taken',
        ];
    }

    public function machine()
    {
        return $this->hasMany('App\Models\Machine','group_id')->orderBy('machine_number');
    }

    public function setSupervisorIdAttribute($value)
    {
        $this->attributes['supervisor_id'] = implode(',',$value);
    }

    public function getSupervisorIdAttribute($value)
    {
        return explode(',',$value);
    }

}
