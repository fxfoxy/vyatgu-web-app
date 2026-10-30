<?php declare(strict_types=1);

namespace App\Library\Tasks;

use App\Library\Database\Session;
use App\Library\Framework\CronTaskAbstract;
use Carbon\Carbon;

class RemoveOldSessionsCronTask extends CronTaskAbstract {
    public function execute(): void {
        $deletedSessionsCount = Session::where('expiration_datetime', '<=', Carbon::now())->delete();
        $this->logger->info("Deleted {$deletedSessionsCount} sessions'");
    }
}
