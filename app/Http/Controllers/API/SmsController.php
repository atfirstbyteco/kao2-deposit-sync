<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
class SmsController extends Controller
{
    public function trigger(Request $request,$accountNo)
    {
        $account = Account::where([
            'account_no' => $accountNo,
            'active' => 1
        ])->firstOrFail();
        $amount = ($request->get('amount'))?$request->get('amount'):10;
        $mobileno = ($request->get('mobileno'))?$request->get('mobileno'):'66XXXXXXXXX';
        $ref = $request->get('ref');

        $account_log_change = $amount;
        $account_log_type = ($account_log_change > 0)?'debit':'credit';
        $account->increment('account_balance', $amount);
        AccountLog::create([
            'account_id' => $account->id,
            'account_log_type' => $account_log_type,
            'account_log_message' => 'SMS '.$mobileno." > ".$amount." THB",
            'account_log_change' => $account_log_change,
            'account_log_balance' => $account->account_balance,
            'active' => true,
        ]);

        $accounts_balance = Account::select(DB::raw('(SUM(account_adjust)+SUM(account_offset)+SUM(account_balance)) as total_donate'))->where([
            'active' => 1
        ])->pluck('total_donate');
        $totalamount = 0;
        foreach($accounts_balance as $account_balance){
            $totalamount += (float) $account_balance;
        }
        Redis::set('balance', $totalamount);

        return response()->json([
            'status'=>'success',
            'ref' => $ref,
            'guid' => (string) Str::uuid(),
        ]);

    }
}
