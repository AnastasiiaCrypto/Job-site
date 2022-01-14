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

class Deposit extends BaseModel
{
	protected $table = 'deposits';
	//protected $with =	['Method','Status'];
    protected $fillable = ['user_id', 'transaction_state_id', 'transaction_receipt', 'deposit_method_id', 'gross', 'fee', 'net', 'json_data', 'wallet_id', 'currency_id', 'currency_symbol','message'];

    public function Transactions(){
        return $this->morphMany('App\Models\Transaction', 'Transactionable');
    }
    
    public function Method(){
    	return $this->hasOne(\App\Models\DepositMethod::class, 'id', 'deposit_method_id');
    }

    public function Status(){
        return $this->hasOne(\App\Models\TransactionState::class, 'id', 'transaction_state_id');
    }

    public function gross(){
        return $this->money_flow .' '. number_format((float)$this->gross, 2, '.', '') .  $this->currency_symbol;
    } 

    public function fee(){
        if ($this->fee > 0) {
            return  '- ' . number_format((float)$this->fee, 2, '.', '') . $this->currency_symbol;
        }
        return number_format((float)$this->fee, 2, '.', '') . $this->currency_symbol;
    }

    public function net(){
         return $this->money_flow .' '. number_format((float)$this->net, 2, '.', '') .  $this->currency_symbol;
    }

}
