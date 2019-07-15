<?php 
	
namespace App\Helpers;
use App\Models\User;
use App\Models\SmsRechargeHistory;
use DB;

class CustomerHelper
{
    public static function adjust_sms(string $customer_id)
    {
        if (!empty($customer_id)) {
        	
        	$sms_history = SmsRechargeHistory::select(
        		DB::raw('(SUM(IF(transaction_type="credit",sms_volume,0)) - SUM(IF(transaction_type="debit",sms_volume,0))) as balance')
        	)->where(['cust_id'=>$customer_id])->get()->toArray();
        	$balance = 0;
        	if (!empty($sms_history['0']['balance'])) {
        		$balance = $sms_history['0']['balance'];
        	}

        	$customer = User::find($customer_id);
        	$customer->sms_balance = $balance;
        	$customer->save();

        }
    }
}

?>