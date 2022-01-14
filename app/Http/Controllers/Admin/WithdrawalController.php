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

namespace App\Http\Controllers\Admin;

use App\Models\Withdrawal;
use App\Models\Currency;
use App\Models\User;
use Larapen\Admin\app\Http\Controllers\PanelController;
use App\Http\Requests\Admin\Request as StoreRequest;
use App\Http\Requests\Admin\Request as UpdateRequest;

class WithdrawalController extends PanelController
{
	public function setup()
	{
		/*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
		$this->xPanel->setModel('App\Models\Withdrawal');
//		$this->xPanel->with(['post', 'package', 'Withdrawals']);
		$this->xPanel->setRoute(admin_uri('withdrawals'));
		$this->xPanel->setEntityNameStrings("Withdrawals", "Withdrawals");
		$this->xPanel->denyAccess(['create', 'delete']);

		$this->xPanel->addButtonFromModelFunction('line', 'view', 'viewBtn', 'beginning');
		
		if (!request()->input('order')) {
			$this->xPanel->orderBy('created_at', 'DESC');
		}

		/*
		|--------------------------------------------------------------------------
		| COLUMNS AND FIELDS
		|--------------------------------------------------------------------------
		*/
		// FIELDS
		$infoLine = [
			'name' => 'info_line_1',
			'type' => 'custom_html',
		];
		$this->xPanel->addField(array_merge($infoLine, [
			'value' => trans('admin::messages.language_info_line_create'),
		]), 'create');
		$this->xPanel->addField(array_merge($infoLine, [
			'value' => trans('admin::messages.language_info_line_update', ['abbr' => request()->segment(3)]),
		]), 'update');

		$countriesFieldParams = [
			'name'       => 'table',
			'label'      => 'Request ID',
			'type'		 => 'custom_html',
			'value'      => $this->getTableData(),
		];

		$this->xPanel->addField($countriesFieldParams);
		
		$this->xPanel->addField([
			'name'              => 'currency_id',
			'label'             => 'Currency',
			'type'              => 'select2',
			'attribute'         => 'name',
			'model'             => 'App\Models\Currency',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-12',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'user_id',
			'label'             => 'User',
			'type'              => 'select2',
			'attribute'         => 'name',
			'model'             => 'App\Models\User',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-12',
			],
		]);

		$this->xPanel->addField([
			'name'              => 'currency_symbol',
			'label'             => 'Currency',
			'type'              => 'select2',
			'attribute'         => 'font_arial',
			'model'             => 'App\Models\Currency',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-12',
			],
		]);

		$this->xPanel->addField([
			'name'              => 'withdrawal_method_id',
			'label'             => 'Withdrawal Method',
			'type'              => 'select2',
			'attribute'         => 'name',
			'model'             => 'App\Models\WithdrawalMethod',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-12',
			],
		]);
		$this->xPanel->addField([
			'name'              => 'transaction_state_id',
			'label'             => 'transaction_states',
			'type'              => 'select2',
			'attribute'         => 'name',
			'model'             => 'App\Models\TransactionState',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-12',
			],
		]);
		// COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'id',
			'label' => "ID",
		]);
		$this->xPanel->addColumn([
			'name'  => 'currency_id',
			'label' => "currency",
			'type'  => "model_function",
			'function_name' => "getCurrencyNameHtml",
		]);
		$this->xPanel->addColumn([
			'name'          => 'user_id',
			'label'         => "User",
			'type'          => 'model_function',
			'function_name' => 'getUserNameHtml',
		]);
		$this->xPanel->addColumn([
			'name'          => 'gross',
			'label'         => "Gross",
		]);
		$this->xPanel->addColumn([
			'name'          => 'fee',
			'label'         => "Free",
		]);
		$this->xPanel->addColumn([
			'name'          => 'net',
			'label'         => "Net",
		]);
		$this->xPanel->addColumn([
			'name'          => 'platform_id',
			'label'         => "Platform Id",
//			'type'          => 'model_function',
//			'function_name' => 'getPlatformHtml',
        ]);
        $this->xPanel->addColumn([
			'name'  => 'created_at',
			'label' => "Created At",
        ]);
        $this->xPanel->addColumn([
			'name'  => 'currency_symbol',
			'label' => "Currency Symbol",
			'type'          => 'model_function',
			'function_name' => 'getCurrencySymbolHtml',
        ]);
        $this->xPanel->addColumn([
			'name'  => 'withdrawal_method_id',
			'label' => "Withdrawal Metohd",
			'type'          => 'model_function',
			'function_name' => 'getWithdrawalHtml',
        ]);
        $this->xPanel->addColumn([
			'name'  => 'transaction_state_id',
			'label' => "Transaction_states",
			'type'          => 'model_function',
			'function_name' => 'geTransactionHtml',
        ]);
        
		// FIELDS
	}
	
	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}
	
	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
	
	public function getTableData()
	{
		$tabledata = Withdrawal::where('id', request()->segment(3))->first();
		if($tabledata == null)
			return;
		else
		{
			$unit = html_entity_decode(Currency::where('id', request()->segment(3))->first()->symbol);
			$user = User::where('id', request()->segment(3))->first()->email;
			return '<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th>Request ID</th>
									<th>Date</th>
									<th>Gross</th>
									<th>Fee</th>
									<th>Net</th>
									<th>Platform Id</th>
									
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>'.$tabledata->id.'</td>
									<td>'.$tabledata->updated_at.'<br></td>
									<td>'.$tabledata->gross.$unit.'</td>
									<td>- '.$tabledata->fee.$unit.'</td>
									<td> '.$tabledata->net.$unit.'</td>
									<td> '.$user.'</td>
								</tr>
							</tbody>
						</table>
					</div>';
		}
	}
	public function getCurrencySymbol()
	{
		return 'aaaaa';
	}

}
