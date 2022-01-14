<?php

namespace App\Models;


use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\StrictActiveScope;
use App\Observer\WithdrawalObserver;
use Larapen\Admin\app\Models\Crud;
use App\Models\Currency;
use App\Models\User;
use App\Models\WithdrawalMethod;
use App\Models\TransactionState;

class Withdrawal extends BaseModel
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */

	use Crud;

	protected $table = 'withdrawals';
	
	protected $guarded = ['id'];
    
    protected $fillable = ['currency', 'user_id','gross', 'fee', 'net','platform_id','currency_symbol','wallet_id', 'active'];

    protected static function boot()
	{
		parent::boot();
		
		Withdrawal::observe(WithdrawalObserver::class);
		
		static::addGlobalScope(new StrictActiveScope());
		static::addGlobalScope(new LocalizedScope());
    }
	
	public function getCurrencyNameHtml()
	{
		return Currency::where('id', $this->currency_id)->first()->name;
	}

	public function getUserNameHtml()
	{
		return User::where('id', $this->user_id)->first()->name;
	}

	public function getCurrencySymbolHtml()
	{
		$currencySymbol = html_entity_decode(Currency::where('id', $this->currency_symbol)->first()->symbol);

		return $currencySymbol;
	}

	public function getWithdrawalHtml()
	{
		$withdrawlMethod = WithdrawalMethod::where('id', $this->withdrawal_method_id)->first()->name;

		return $withdrawlMethod;
	}

	public function geTransactionHtml()
	{
		$transactionState = TransactionState::where('id', $this->transaction_state_id)->first()->name;

		return $transactionState;
	}

	public function viewBtn($xPanel = false)
	{
		$url = admin_url('withdrawal/view/' . $this->id);
		
		$msg = "view";
		$tooltip = ' data-toggle="tooltip" title="' . $msg . '"';
		
		$out = '';
		$out .= '<a class="btn btn-xs btn-warning" href="' . $url . '"' . $tooltip . '>';
		$out .= '<i class="fa fa-eye"></i> ';
		$out .= mb_ucfirst('view');
		$out .= '</a>';
		
		return $out;
	}
}
