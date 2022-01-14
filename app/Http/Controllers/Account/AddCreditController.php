<?php

namespace App\Http\Controllers\Account;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\DepositMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AddCreditController extends AccountBaseController
{
    public function addCreditForm( $method_id = false ){
				
        //$methods = Auth::user()->getAuthUserCurrency()->DepositMethods()->get();
       		
		$methods= DB::table('deposit_methods')
                  ->join('currency_deposit_methods', 'deposit_methods.id', '=', 'currency_deposit_methods.deposit_method_id')                 
                  ->select('deposit_methods.*', 'currency_deposit_methods.deposit_method_id')     
                  ->where('currency_deposit_methods.currency_id', Auth::user()->currency_id)                  
                  ->get();
		
		
    	if ($method_id) {

    		$current_method = DepositMethod::where('id', $method_id)->first();

    		if ($current_method == null) {
    			dd('please contact admin to link a deposit method to '.Auth::user()->currentCurrency()->name.' currency');
    		}
    	}else{
            if (isset($methods[0]) ) {
               $current_method = $methods[0];
            } else{
                dd('please contact admin to link a deposit method to '.Auth::user()->currentCurrency()->name.' currency');
            }
    	}

    	
        $currencies = Currency::where('id' , '!=', Auth::user()->currency_id)->get();
		$currencyName = Currency::where('id', Auth::user()->currency_id)->first()->name;
		$currencyCode = Currency::where('id', Auth::user()->currency_id)->first()->code;
    	return view('deposits.addCreditForm')
    	->with('current_method', $current_method)
        ->with('currencies', $currencies)
        ->with('currencyName', $currencyName)
        ->with('currencyCode', $currencyCode)
    	->with('methods', $methods);
    }

    public function depositRequest( Request $request){

    	$this->validate($request, [
    		'deposit_method'	=> 'required|integer|exists:deposit_methods,id',
            'deposit_currency'  => 'required|integer|exists:currencies,id',
    		'deposit_screenshot'	=> 'required|mimes:jpg,png,jpeg|max:5128',
            'message'   =>  'required',
    	]);

        $wallet = Wallet::where('currency_id', $request->deposit_currency)->where('user_id', Auth::user()->id)->first();

    	if ( $request->hasFile('deposit_screenshot') ) {
    		$file = $request->file('deposit_screenshot');
    		$path = 'users/'.Auth::user()->name.'/deposits/'.$file->getClientOriginalName();
    		Storage::put($path, $file);

    		$local_path = Storage::put($path, $file);

    		$link = Storage::url($local_path);
    	}
		$currencySymbol = html_entity_decode(Currency::where('id', Auth::user()->currency_id)->first()->symbol);

    	Deposit::create([
    		'user_id'	=>	Auth::user()->id,
            'wallet_id' =>  $wallet->id,
            'currency_id'   =>  Auth::user()->currency_id, 
            'currency_symbol'   =>  $currencySymbol,
    		'transaction_state_id'	=>	3,
    		'deposit_method_id'	=>	$request->deposit_method,
    		'gross'	=>	0,
    		'fee'	=>	0,
    		'net'	=>	0,
            'message'   =>  $request->message,
    		'transaction_receipt'	=>	$link,
    		'json_data'	=>	'{"deposit_screenshot":"'.$path.'"}'
    	]);

    	flash('Your Deposit is Waiting for a review', 'info');

    	return  redirect(route('mydeposits'));

    }
	
	 public function wallet($id){

        $currency = Auth::user()->walletByCurrencyId($id);
        if ($currency) {
            
            Auth::user()->currency_id = $id;
            Auth::user()->save();
        }
		return back();
        // return  redirect(route('add.credit'));
    }
	
	
}
