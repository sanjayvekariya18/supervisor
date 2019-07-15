<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class SmsRechargeHistory extends Model
{
    use Sortable;
    
    public $sortable = [
        
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
    ];

    public static function admin_rules()
    {
        return [
            'transaction_type'  => ['required',Rule::in(['credit', 'debit'])],
            'sms_volume'        => 'required|min:1|max:5000|integer',
            'note'              => 'required|max:255|min:1',
        ];
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin','activity_by');
    }

    
}
