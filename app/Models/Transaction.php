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


class Transaction extends BaseModel
{	
	protected $table = 'transactionable';
    //protected $with = ['Status'];
    
    protected $fillable = [
        'user_id',
        'entity_id',
        'entity_name',
        'currency',
        'balance',
        'activity_title',
        'money_flow',
        'transaction_state_id',
        'request_id',
        'gross',
        'fee',
        'net',
        'json_data',
        'currency_id',
        'currency_symbol'];


    public function Transactionable(){
    	return $this->morph();
    }

    public function Status(){
        return $this->hasOne(\App\Models\TransactionState::class, 'id', 'transaction_state_id');
    }

    public function User(){
    	return $this->belongsTo(\App\Models\User::class);
    }

    public function gross(){
        return $this->money_flow .' '. number_format((float)$this->gross, 2, '.', ',') .' '.  $this->currency_symbol;
    } 


    public function fee(){
        if ($this->fee > 0) {
            return  '- ' . number_format((float)$this->fee, 2, '.', ',') .' '. $this->currency_symbol;
        }
        return number_format((float)$this->fee, 2, '.', ',') . ' '. $this->currency_symbol;
    }

    public function net(){
         return $this->money_flow .' '. number_format((float)$this->net, 2, '.', ',') . ' '. $this->currency_symbol;
    }

    public function balance(){
        return number_format((float)$this->balance, 2, '.', ',') . ' '. $this->currency_symbol;
    }
}
