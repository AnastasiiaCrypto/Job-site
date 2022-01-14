<?php
namespace App\Models;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\StrictActiveScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\Traits\CountryTrait;
use App\Notifications\ResetPasswordNotification;
use App\Observer\TransactionStateObserver;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;
use Jenssegers\Date\Date;
use Larapen\Admin\app\Models\Crud;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class TransactionState extends BaseModel
{
    //
    use Crud;

    protected $table = 'transaction_states';
    
    protected $primaryKey = 'id';

    protected static function boot()
	{
		parent::boot();
		
		TransactionState::observe(TransactionStateObserver::class);
		
//		static::addGlobalScope(new StrictActiveScope());
		static::addGlobalScope(new LocalizedScope());
    }
	
}
