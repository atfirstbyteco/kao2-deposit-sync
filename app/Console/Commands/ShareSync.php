<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\ScbDeposit;
use App\Models\Account;
use App\Models\AccountLog;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
class ShareSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kao:share:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Share a last deposit balance';

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
        $share = $this->deposit->updateShare();
        dd($share);
        $this->info($share);

    }
}
