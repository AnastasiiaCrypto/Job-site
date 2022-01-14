<?php

namespace App\Http\Controllers\Account;

use Auth;
use App\Models\User;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\CurrencyExchangeRate;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ExchangeController extends AccountBaseController
{


    public function getExchangeRequestForm(Request $request, $currency_id = null, $second_currency_id = null ){
    	
    	$firstCurrenciesExchages = CurrencyExchangeRate::with('firstCurrency')->distinct()->get();

    	if (is_null($firstCurrenciesExchages)) {
    		dd('Please Contact admin to add currency exchange rates');
    	}

    	if (is_null($currency_id) or !($currency_id)) {
			//dd("true");
    		$firstCurrency = $firstCurrenciesExchages[0]->first_currency_id;
    	}else{
			//dd("false");
    		$firstCurrency = $currency_id;
    	}


    	$secondCurrenciesExchanges = CurrencyExchangeRate::with('secondCurrency')->where('first_currency_id', $firstCurrency)->get();

        if (is_null($second_currency_id) or !($second_currency_id)) {
            $secondCurrency = $secondCurrenciesExchanges[0]->second_currency_id;
        }else{
            $secondCurrency = $second_currency_id;
        }

        if ($firstCurrency == $secondCurrency) {
            return back();
        }

        Auth::user()->currency_id = $firstCurrency;
        Auth::user()->save();

        $wallet = Wallet::with('Currency')->where('user_id', Auth::user()->id)->where('currency_id',$secondCurrency)->first();

        if ($wallet == null) {
            $wallet = Auth::user()->newWallet($secondCurrency);
        }
        

        $exchange = CurrencyExchangeRate::where('first_currency_id', $firstCurrency)->where('second_currency_id', $secondCurrency)->first();
		
		$secondCurrencyName = Currency::where('id', $secondCurrency)->first()->name;
		
		$secondCurrencySymbol = html_entity_decode(Currency::where('id', $secondCurrency)->first()->symbol);
		$secondCurrencyCode = Currency::where('id', $secondCurrency)->first()->code;
		$firstCurrencyName = Currency::where('id', $firstCurrency)->first()->name;
		$firstCurrencySymbol = html_entity_decode(Currency::where('id', $firstCurrency)->first()->symbol);
		$firstCurrencyCode = Currency::where('id', $firstCurrency)->first()->code;
       
        return view('account.exchangeRequestForm')
        ->with('wallet',$wallet)
        ->with('exchange', $exchange)
        ->with('secondCurrencyName',$secondCurrencyName)
        ->with('firstCurrencyName',$firstCurrencyName)
		->with('firstCurrencyCode',$firstCurrencyCode)
		->with('secondCurrencyCode',$secondCurrencyCode)
		->with('secondCurrencySymbol',$secondCurrencySymbol)
        ->with('firstCurrencySymbol',$firstCurrencySymbol)
		->with('secondCurrency',$secondCurrency)
        ->with('firstCurrency',$firstCurrency)
        ->with('secondCurrenciesExchanges',$secondCurrenciesExchanges)
        ->with('firstCurrenciesExchages',$firstCurrenciesExchages);
    }

    public function exchange(Request $request){

        $this->validate($request, [
            'amount'    =>  'required|numeric|between:1,'.Auth::user()->balance(),
            'exchange_id'   =>  'required|exists:currency_exchange_rates,id'
        ]);

        $currencyexchange = CurrencyExchangeRate::with('firstCurrency','secondCurrency')->find($request->exchange_id);

        $firstWallet = Wallet::where('currency_id', $currencyexchange->first_currency_id)->where('user_id', Auth::user()->id)->first();

        $secondWallet = Wallet::where('currency_id', $currencyexchange->second_currency_id)->where('user_id', Auth::user()->id)->first();

        $firstWallet->amount = $firstWallet->amount - ($request->amount);

        $secondWallet->amount = $secondWallet->amount + ( $request->amount * $currencyexchange->exchanges_to_second_currency_value );

        $firstWallet->save();
        $secondWallet->save();
		
		$firstCurrencyName = Currency::where('id', $currencyexchange->first_currency_id)->first()->name;
		$firstCurrencySymbol = html_entity_decode(Currency::where('id', $currencyexchange->first_currency_id)->first()->symbol);
		$secondCurrencySymbol = html_entity_decode(Currency::where('id', $currencyexchange->second_currency_id)->first()->symbol);
		$secondCurrencyName = Currency::where('id', $currencyexchange->second_currency_id)->first()->name;
				
        $exchange = Exchange::create([
            'user_id'   =>  Auth::user()->id,
            'first_currency_id' =>   $currencyexchange->first_currency_id,
            'second_currency_id'    =>  $currencyexchange->second_currency_id,
            'gross' =>  $request->amount,
            'fee'   =>  0.00,
            'net'   =>  $request->amount,

        ]);

        Auth::User()->RecentActivity()->save($exchange->Transactions()->create([
            'user_id' => Auth::User()->id,
            'entity_id'   =>  $exchange->id,
            'entity_name' =>   $firstCurrencyName,
            'transaction_state_id'  =>  1, // waiting confirmation
            'money_flow'    => '-',
            'currency_id' =>  $currencyexchange->first_currency_id,
            'currency_symbol' =>  $firstCurrencySymbol,
            'activity_title'    =>  'Currency Exchange',
            'gross' =>  $exchange->gross,
            'fee'   =>  $exchange->fee,
            'net'   =>  $exchange->net,
            'balance'   =>  $firstWallet->amount
        ]));

        Auth::User()->RecentActivity()->save($exchange->Transactions()->create([
            'user_id' => Auth::User()->id,
            'entity_id'   =>  $exchange->id,
            'entity_name' =>   $secondCurrencyName,
            'transaction_state_id'  =>  1, // waiting confirmation
            'money_flow'    => '+',
            'currency_id' =>  $currencyexchange->second_currency_id,
            'currency_symbol' =>  $secondCurrencySymbol,
            'activity_title'    =>  'Currency Exchange',
            'gross' =>  $request->amount * $currencyexchange->exchanges_to_second_currency_value,
            'fee'   =>  $exchange->fee,
            'net'   =>  $request->amount * $currencyexchange->exchanges_to_second_currency_value,
            'balance'   =>  $secondWallet->amount
        ]));

        return redirect('account/transactions');


    }
}
