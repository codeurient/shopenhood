<?php

namespace App\Console\Commands;

use App\Services\ListingService;
use Illuminate\Console\Command;

class PurgeDeletedListingsCommand extends Command
{
    protected $signature = 'listings:purge-deleted';

    protected $description = 'Permanently delete listings that have been soft-deleted past the retention period';

    public function handle(ListingService $listingService): int
    {
        $count = $listingService->purgeOldDeletedListings();

        $this->info("Purged {$count} listing(s).");

        return self::SUCCESS;
    }
}
