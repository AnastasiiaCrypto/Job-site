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
use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class Wallet extends BaseModel
{
	protected $table = 'wallets';
	protected $fillable = ['user_id', 'amount', 'currency_id'];

	public function User(){
		return $this->belongsTo(User::class);
	}

	public function Currency(){
		return $this->belongsTo(Currency::class);
	}
	
	public function getCurrencySymbol(){
		return Currency::where('id', $this->currency_id)->first()->symbol;
	}
	public function getCurrencyName(){
		return Currency::where('id', $this->currency_id)->first()->name;
	}

}