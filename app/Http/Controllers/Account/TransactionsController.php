<?php
/**
 * JobClass - Geolocalized Job Board Script
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Account;

use Auth;
use App\Models\User;
use Torann\LaravelMetaTags\Facades\MetaTag;

class TransactionsController extends AccountBaseController
{
	private $perPage = 10;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->perPage = (is_numeric(config('settings.listing.items_per_page'))) ? config('settings.listing.items_per_page') : $this->perPage;
	}
	
	/**
	 * List Transactions
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		
		 $transactions = Auth::user()->RecentActivity()->with('Status')->orderby('created_at','desc')->where('transaction_state_id', '!=', 3)->paginate(10);

        $transactionsToConfirm =  Auth::user()->RecentActivity()->with('Status')->orderby('created_at','desc')->where('transaction_state_id', 3)->where('money_flow' , '!=', '+')->paginate(10);

        // return view('home.index')
        // ->with('transactions', $transactions)
        // ->with('transactions_to_confirm', $transactionsToConfirm);
		
		$data = [];
		//$data['transactions'] = $this->transactions->paginate($this->perPage);
		$data['transactions'] = $transactions;
		$data['transactions_to_confirm'] = $transactionsToConfirm;
		
		view()->share('pagePath', 'transactions');
		
		// Meta Tags
		MetaTag::set('title', t('My Transactions'));
		MetaTag::set('description', t('My Transactions on :app_name', ['app_name' => config('settings.app.app_name')]));
		
		return view('account.transactions', $data);
	}
	
	
}
