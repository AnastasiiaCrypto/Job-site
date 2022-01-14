<?php

namespace App\Models;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Traits\CountryTrait;
use App\Notifications\ResetPasswordNotification;
use App\Observer\UserObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;
use Jenssegers\Date\Date;
use Larapen\Admin\app\Models\Crud;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User;
class CurrencyExchangeRate extends BaseModel
{
	public function firstCurrency(){

		return $this->belongsTo(Currency::class, 'first_currency_id');

	}
	
	public function firstCurrencyData(){
		return Currency::where('id', $this->first_currency_id)->first();
	}
	
public function secondCurrencyData(){
		return Currency::where('id', $this->second_currency_id)->first();
	}
	


	public function secondCurrency(){

		return $this->belongsTo(Currency::class, 'second_currency_id');

	}

}