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

class Exchange extends BaseModel
{
	protected $table = 'exchanges';

    protected $fillable = ['user_id', 'first_currency_id', 'second_currency_id',  'gross', 'fee', 'net'];

    public function Transactions(){
        return $this->morphMany('App\Models\Transaction', 'Transactionable');
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