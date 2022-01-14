<?php

namespace App\Helpers;

use Auth;

class Money
{
    public function value($val,  $currency_symbol = null){
      if (!is_null($currency_symbol) and  $currency_symbol == '(BTC)') {
     		return $val .' '. $currency_symbol;
      }
       return  number_format((float)$val, 2, '.', ',') . ' '. $currency_symbol;
    }
   public static function instance(){

       return new Money();
   }
}