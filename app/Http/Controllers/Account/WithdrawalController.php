<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Models\WithdrawalMethod;

class WithdrawalController extends Controller
{
    public function index(Request $request){dd('agsdgsd');
    	$withdrawals = Withdrawal::with(['Method','Status'])->where('user_id', Auth::user()->id)->orderby('id', 'desc')->paginate(10);
    	return view('withdrawals.index')
    	->with('withdrawals', $withdrawals);

    }

    public function getWithdrawalRequestForm(Request $request, $method_id = false){

    	 $methods = Auth::user()->currentCurrency()->WithdrawalMethods()->get();
        if ($method_id) {

            $current_method = WithdrawalMethod::where('id', $method_id)->first();

            if ($current_method == null) {
                dd('please contact admin to link a withdrawal method to '.Auth::user()->currentCurrency()->name.' currency');
            }
        }else{
            if (isset($methods[0]) ) {
               $current_method = $methods[0];
            } else{
                dd('please contact admin to link a withdraw method to '.Auth::user()->currentCurrency()->name.' currency');
            }
        }

        
        $currencies = Currency::where('id' , '!=', Auth::user()->currentCurrency()->id)->get();

    	return view('withdrawals.withdrawalRequestForm')
    	->with('current_method', $current_method)
        ->with('currencies', $currencies)
    	->with('methods', $methods);
    }

    public function makeRequest(Request $request){

        $this->validate($request, [
            'withdrawal_method' => 'integer|exists:withdrawal_methods,id',
            'platform_id' => 'required',
            'withdrawal_currency'   =>  'required|integer|exists:currencies,id',
            'amount'   =>  'required|numeric|between:5,'.(float)Auth::user()->balance,
        ]);

         if ( Auth::user()->account_status == 0 ) {
            flash('Your account is under a withdrawal request review proccess. please wait for a few minutes and try again' , 'info');
             return  back();
        }

        $current_method = WithdrawalMethod::findOrFail($request->withdrawal_method);
        $wallet = Wallet::where('user_id', Auth::user()->id)->where('currency_id', Auth::user()->currentCurrency()->id)->first();

        $fee = (($current_method->percentage_fee/100)* $request->amount) + $current_method->fixed_fee ; 
    	
        Withdrawal::create([
            'user_id'   =>  Auth::user()->id,
            'transaction_state_id'  =>  3,
            'withdrawal_method_id'  =>  $request->withdrawal_method,
            'platform_id'  =>  $request->platform_id,
            'gross' =>  $request->amount,
            'fee'   =>  $fee,
            'currency_id'   =>  Auth::user()->currentCurrency()->id,
            'currency_symbol'   =>  Auth::user()->currentCurrency()->symbol,
            'wallet_id' => $wallet->id,
            'net'   =>  $request->amount - $fee,
        ]);

        return redirect(route('withdrawal.index'));
    }

    public function confirmWithdrawal(Request $request){
        
        if (!Auth::user()->isAdministrator()) {
            abort (404);
        }

        $withdrawal = Withdrawal::with('Method')->findOrFail($request->id);

        if ($withdrawal->transaction_state_id == 1 ) {
            flash('Transaction Already completed !', 'info' );
            //return redirect(url('/').'/admin/withdrawals/'.$withdrawal->id);

            return back();
        }

        $user = User::findOrFail($request->user_id);

        $wallet = Wallet::where('user_id', Auth::user()->id)->where('currency_id',$user->currentCurrency()->id)->first();

        if ($wallet->amount < $withdrawal->gross) {
            flash('User doesen\'t have enought funds to withdraw '.$withdrawal->gross.' $', 'danger' );

            return back();
        }

        $wallet->amount = (double)$wallet->amount - (double)$withdrawal->gross;

        $user->RecentActivity()->save($withdrawal->Transactions()->create([
            'user_id' => $user->id,
            'entity_id'   =>  $user->id,
            'entity_name' =>  $withdrawal->Method->name,
            'transaction_state_id'  =>  1, // waiting confirmation
            'money_flow'    => '-',
            'activity_title'    =>  'Withdrawal',
            'balance'   =>   $user->balance,
            'gross' =>  $withdrawal->gross,
            'fee'   =>  $withdrawal->fee,
            'net'   =>  $withdrawal->net,
            'currency_id'   =>  $withdrawal->currency_id,
            'currency_symbol'   =>  $withdrawal->currency_symbol,
        ]));

        
        $withdrawal->transaction_state_id = 1;

        $withdrawal->save();
        $user->account_status = 1;
        $wallet->save();
        
        return redirect(url('/').'/admin/withdrawals/'.$withdrawal->id);
        
    }
}
