<?php

namespace App\Http\Controllers\Account;
use Auth;
use App\Models\User;
use App\Models\Send;
use App\Models\Currency;
use App\Models\Receive;
use App\Models\Setting;
use App\Models\Transaction;
use Validator;
use Illuminate\Http\Request;

class MoneyTransferController extends AccountBaseController
{
    public function sendMoneyForm(){
    	return view('sendmoney.index');
    }

    public function sendMoney(Request $request){
    
        // $this->validate($request, [
            // 'amount'    =>  'required|numeric|between:0,500.00',
            // 'description'   =>  'required|string',
            // 'email' =>  'required|email|exists:users,email',
        // ]);
	
        $currency = Currency::where('id', Auth::user()->currency_id)->first();

        $auth_wallet = Auth::user()->walletByCurrencyId(Auth::user()->currency_id);

        if((boolean)$currency == false ){
          flash('Wops, something went wrong... looks like we do not support this currency. please contact support if this error persists !', 'danger');
            return back();
        }

        if ( Auth::user()->account_status == 0 ) {
            flash('Your account is under a withdrawal request review proccess. please wait for a few minutes and try again' , 'info');
             return  back();
        }


        if ($request->email == Auth::user()->email) {
            flash('You can\'t send money to the same account you are in' , 'danger');
            return  back();
        } 

        if ($request->amount > $auth_wallet->amount) {
            flash('You have insufficient funds to send <strong>'.$request->amount.' to '.$request->email .'</strong>', 'danger');
            return  back();
        }

        $user = User::where('email', $request->email)->first();
		 
        $currencySymbol = html_entity_decode(Currency::where('id', Auth::user()->currency_id)->first()->symbol);
        $send_fee = 0; //free to send money
		$percentageFee = Setting::where('key', 'money-transfers.mt_percentage_fee')->first()->value;
		$fixedFee = Setting::where('key', 'money-transfers.mt_fixed_fee')->first()->value;
		
        $receive_fee = (((double)$percentageFee/100)* (double)$request->amount) + (double)$fixedFee ;

        //dd($receive_fee);

        $receive = Receive::create([
            'user_id'   =>   $user->id,
            'from_id'        => Auth::user()->id,
            'transaction_state_id'  =>  3, // waiting confirmation
            'gross'    =>  $request->amount,
            'currency_id' =>  Auth::user()->currency_id,
            'currency_symbol' =>  $currencySymbol,
            'fee'   =>  $receive_fee,
            'net'   =>  $request->amount - $receive_fee,
            'description'   =>  $request->description,
            'send_id'    =>  0
        ]);

        $send = Send::create([
            'user_id'   =>  Auth::user()->id,
            'to_id'        =>  $user->id,
            'transaction_state_id'  =>  3, // waiting confirmation 
            'gross'    =>  $request->amount,
            'currency_id' =>  Auth::user()->currency_id,
            'currency_symbol' =>  $currencySymbol,
            'fee'   =>  $send_fee,
            'net'   =>  $request->amount - $send_fee,
            'description'   =>  $request->description,
            'receive_id'    =>  $receive->id
        ]);

        $user->RecentActivity()->save($receive->Transactions()->create([
            'user_id' => $receive->user_id,
            'entity_id'   =>  $receive->from_id,
            'entity_name' =>  Auth::user()->name,
            'transaction_state_id'  =>  3, // waiting confirmation
            'money_flow'    => '+',
            'currency_id' =>  Auth::user()->currency_id,
            'currency_symbol' =>  $currencySymbol,
            'activity_title'    =>  'Payment Received',
            'gross' =>  $receive->gross,
            'fee'   =>  $receive->fee,
            'net'   =>  $receive->net,
        ]));

        Auth::user()->RecentActivity()->save($send->Transactions()->create([
            'user_id' =>  Auth::user()->id,
            'entity_id'   =>  $receive->from_id,
            'entity_name' =>  $user->name,
            'transaction_state_id'  =>  3, // waiting confirmation
            'money_flow'    => '-',
            'currency_id' =>  Auth::user()->currency_id,
            'currency_symbol' =>  $currencySymbol,
            'activity_title'    =>  'Payment Sent',
            'gross' =>  $send->gross,
            'fee'   =>  $send->fee,
            'net'   =>  $send->net
        ]));
       
		return redirect('account/transactions');
    }

    public function sendMoneyConfirm(Request $request){
        $this->validate($request, [
            'tid'   => 'required|numeric',
        ]);

        $transaction = Transaction::find($request->tid);

        $currency = Currency::find($transaction->currency_id);

        $auth_wallet = Auth::user()->walletByCurrencyId($currency->id);


        if((boolean)$transaction == false ){
            flash('Wops, something went wrong... please contact support if this error persists !', 'danger');
            return back();
        }

        if ( Auth::user()->account_status == 0 ) {
            flash('Your account is under a withdrawal request review proccess. please wait for a few minutes and try again' , 'info');
             return  back();
        }
        
        if(Auth::user()->id != $transaction->user_id ){
            flash('Wops, something went wrong... please contact support if this error persists !');
            return back();
        }

        $send = Send::find($transaction->transactionable_id);

         if((boolean)$send == false ){
            flash('Wops, something went wrong... please contact support if this error persists !', 'danger');
            return back();
        }

        if(Auth::user()->id != $send->user_id ){
            flash('Wops, something went wrong... please contact support if this error persists !');
            return back();
        }

        $receive = Receive::find($send->receive_id);

        if((boolean)$receive == false ){
            flash('Wops, something went wrong... please contact support if this error persists !', 'danger');
            return back();
        }

        $user = User::find($receive->user_id);

        $user_wallet = $user->walletByCurrencyId($currency->id);

        if((boolean)$user == false ){
            flash('Wops, something went wrong... please contact support if this error persists !', 'danger');
            return back();
        }

        $receive_transaction = transaction::where('transactionable_type', 'App\Models\Receive')->where('user_id', $user->id)->where('transaction_state_id', 3)->where('money_flow', '+')->where('transactionable_id', $receive->id)->first();

        if((boolean)$receive_transaction == false ){
            flash('Wops, something went wrong... please contact support if this error persists !', 'danger');
            return back();
        }

        if((double)$auth_wallet->amount < (double)$transaction->net ){
             flash('You have insufficient funds to send <strong>'.$request->amount.' to '.$request->email .'</strong>', 'danger');
            return  back();
        }

        $receive->send_id = $send->id;
        $receive->transaction_state_id = 1;
        $receive->save();

        $send->transaction_state_id = 1;
        $send->save();

        $transaction->transaction_state_id = 1;
        $transaction->balance = (double)$auth_wallet->amount - (double)$transaction->net;
        $transaction->save();

        $receive_transaction->transaction_state_id = 1;
        $receive_transaction->balance =  (double)  $user_wallet->amount + $receive_transaction->net;
        $receive_transaction->save();

        $auth_wallet->amount = (double)$auth_wallet->amount - (double)$transaction->net ;
        $auth_wallet->save();

        $user_wallet->amount =  $user_wallet->amount + $receive_transaction->net ;
        $user_wallet->save();


        flash('Transaction Complete ', 'success');

        return  back();
    }
}
