<?php

namespace App\Console\Commands;

use App\Services\CertificateService;
use Illuminate\Console\Command;

class CheckCertificateExpiry extends Command
{
    protected $signature = 'capacity:check-certificates';

    protected $description = 'Scan all workforce certifications for 30-day expiry deadlines and expired statuses';

    public function handle(CertificateService $certificateService): int
    {
        $this->info('Scanning workforce certifications...');
        $results = $certificateService->checkAndNotifyExpiries();

        $this->info("Scan complete: {$results['expiring']} marked expiring soon, {$results['expired']} marked expired.");

        return Command::SUCCESS;
    }
}
