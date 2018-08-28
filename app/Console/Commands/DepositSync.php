<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\ScbDeposit;
use App\Models\Account;
use App\Models\AccountLog;
use Illuminate\Support\Facades\Redis;
class DepositSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kao:deposit:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync a last deposit balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ScbDeposit $deposit)
    {
        parent::__construct();
        $this->deposit = $deposit;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = Account::where([
            'account_type' => 'scbdeposit',
            'account_autosync' => true,
            'active' => 1
        ])->get();

        foreach($accounts as $account){

            $deposit = $this->deposit->getBalance($account->account_no);

            if($deposit){
                $amount =  $deposit->balanceInfo->accountBalances[0]->amount;
                $account_log_change = floatval($amount-$account->account_balance);
                $account_log_type = ($account_log_change > 0)?'debit':'credit';
                    $account->update([
                        'account_balance' => $amount
                    ]
                );
                AccountLog::create([
                    'account_id' => $account->id,
                    'account_log_type' => $account_log_type,
                    'account_log_message' => 'auto sync',
                    'account_log_change' => $account_log_change,
                    'account_log_balance' => $account->account_balance,
                    'active' => true,
                ]);
                $this->info("Sync account ".$account->account_no." > ".$amount." THB");

            }
        }
        $accounts_balance = Account::where([
            'active' => 1
        ])->pluck('account_balance');
        $totalamount = 0;
        foreach($accounts_balance as $account_balance){
            $totalamount += (float) $account_balance;
        }
        Redis::set('devtestbalance', $totalamount);

    }
}
