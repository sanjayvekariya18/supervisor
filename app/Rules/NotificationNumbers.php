<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotificationNumbers implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        // Check if value is not empty
        if (!empty($value)) {
            $phone_numbers = explode(",",$value);

            // Check for valid length of the Phone numbers
            foreach ($phone_numbers as $key => $number) {
                if (strlen($number) != 10) {
                    return false;
                }
            }

            // Check if Numbers are not more than 20
            if (count($phone_numbers)>20) {
                return false;
            }

        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'One of the numbers is not valid OR Max 20 numbers are allowed.';
    }
}
