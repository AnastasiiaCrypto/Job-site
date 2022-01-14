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

class Receive extends BaseModel
{
	protected $table = 'receives';
	protected $with = ['User', 'From'];
    protected $fillable = ['user_id', 'from_id', 'send_id', 'transaction_state_id', 'gross', 'fee', 'net',	'description', 'json_data','currency_id', 'currency_symbol'];

    public function User(){
    	return $this->belongsTo(\App\Models\User::class);
    }

    public function From(){
    	return $this->belongsTo(\App\Models\User::class, 'from_id');
    }

    public function Transactions(){
        return $this->morphMany('App\Models\Transaction', 'Transactionable');
    }
}
