<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotificationNumbers;

class User extends Authenticatable
{
    use Notifiable;
    //use SoftDeletes;
    use Sortable;
    
    public $incrementing = false;

    public $sortable = [
        'id',
        'first_name',
        'sms_balance',
        'email',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','first_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public static function get_shifts($user_id='')
    {
        return User::select(['day_shift','night_shift','disconnect_alert'])->where(['id'=>$user_id])->get()->toArray();
    }

    public function getPermissionAttribute($value)
    {
        return (object)json_decode($value);
    }

    public function setPermissionAttribute($value)
    {
        $this->attributes['permission'] = json_encode($value);
    }

    public static function rules()
    {
        return [
            'id'                                => 'required|max:20|min:3|unique:users,id',
            'company_name'                      => 'required|max:30|min:3',
            'permission_id'                      => 'required',
            'first_name'                        => 'required|max:100|min:3',
            'contact_number_1'                  => 'required|digits:10',
            'username'                          => 'required|max:50|min:3|unique:users,username',
            'password_disp'                     => 'required|max:50|min:3',
            'email'                             => 'nullable|email|max:100|min:3|unique:users,email',
            'address_1'                         => 'max:255|min:0',
            'address_2'                         => 'max:255|min:0',
        ];
    }

    public static function final_rules()
    {
        $rules = self::rules();

        $rules['contact_number_1']                  = 'required|digits:10|unique:users,contact_number_1';
        $rules['sms_notification_numbers']          = ['nullable', new NotificationNumbers];
        $rules['whatsapp_notification_numbers']     = ['nullable', new NotificationNumbers];

        return $rules;
    }


    public static function update_rules($cust_id = 0)
    {
        return [
            // 'permission_id'                      => 'required',
            'first_name'                    => 'required|max:100|min:3',
            'company_name'                  => 'required|max:30|min:3',
            'contact_number_1'              => 'required|digits:10|unique:users,contact_number_1,'.$cust_id,
            'email'                         => 'nullable|email|max:100|min:3|unique:users,email,'.$cust_id,
            // 'password_disp'                 => 'required|max:50|min:3',
            'sms_notification_numbers'      => ['nullable', new NotificationNumbers],
            'whatsapp_notification_numbers' => ['nullable', new NotificationNumbers],
            'address_1'                     => 'max:255|min:0',
            'address_2'                     => 'max:255|min:0',
        ];
    }

    public static function change_password()
    {
        return [
            'old_password'      => 'required|max:20|min:3',
            'new_password'      => 'required|max:20|min:3',
            'confirm_password'  => 'required|max:20|min:3|same:new_password',
        ];
    }

    public static function supervisor_rules()
    {
        return [
            'id'                                => 'required|max:20|min:3|unique:users,id',
            'first_name'                        => 'required|max:100|min:3',
            'contact_number_1'                  => 'required|digits:10|unique:users,contact_number_1',
            'username'                          => 'required|max:50|min:3|unique:users,username',
            'password_disp'                     => 'required|max:50|min:3',
            'email'                             => 'nullable|email|max:100|min:3|unique:users,email',
            'address_1'                         => 'max:255|min:0',
            'address_2'                         => 'max:255|min:0',
        ];
    }

    public static function supervisor_update_rules($cust_id = 0)
    {
        return [
            'first_name'                    => 'required|max:100|min:3',
            'contact_number_1'              => 'required|digits:10|unique:users,contact_number_1,'.$cust_id,
            'email'                         => 'nullable|email|max:100|min:3|unique:users,email,'.$cust_id,
            // 'password_disp'                 => 'required|max:50|min:3',
            'sms_notification_numbers'      => ['nullable', new NotificationNumbers],
            'whatsapp_notification_numbers' => ['nullable', new NotificationNumbers],
            'address_1'                     => 'max:255|min:0',
            'address_2'                     => 'max:255|min:0',
        ];
    }

    public static function messages()
    {
        return [
            'id.required' => 'Customer ID is required',
            'id.unique' => 'Customer ID already taken',
            'password_disp.required' => 'Password is required',
        ];
    }

    public function generateToken($token,$deviceType)
    {
        $this->token = $token;
        $this->device_type = $deviceType;
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }

    public function machine_group()
    {
        return $this->hasMany('App\Models\MachineGroup','supervisor_id');
    }

    public function machines()
    {
        return $this->hasMany('App\Models\Machine','cust_id','id');
    }

    public function model_permission()
    {
        return $this->belongsTo('App\Models\Permission');
    }

}
