<?php

namespace App\Validators;

use App\Hk01\Payment\CreditCard;
use Carbon\Carbon;
use Illuminate\Validation\Validator;
use Exception;

class CreditCardValidator
{

    public function validateCurrency($attribute, $value, $parameters, $validator)
    {
        $number = array_get($validator->getData(), 'ccnumber');
        if (empty($number) || CreditCard::parse($number)->getType() == 'amex' && $value != 'USD') {
            return false;
        }
        return true;
    }

    public function validateNumber($attribute, $value, $parameters, $validator)
    {
        return CreditCard::parse($value)->isValid();
    }

    public function validateExpDate($attribute, $value, $parameters, Validator $validator)
    {
        if ($attribute === 'ccmonth') {
            $month = $value;
            $year = array_get($validator->getData(), 'ccyear');
            if (empty($year)) {
                return false;
            }
        } else {
            return false;
        }

        try {
            return Carbon::now()->diffInMonths(Carbon::createFromDate($year, $month, 1), false) >= 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function validateCvv($attribute, $value, $parameters, Validator $validator)
    {
        $number = array_get($validator->getData(), 'ccnumber');
        if (empty($number)) {
            return false;
        }
        return CreditCard::parse($number)->checkCvv($value);
    }

}