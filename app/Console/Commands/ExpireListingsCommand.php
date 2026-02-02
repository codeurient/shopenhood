<?php

namespace App\Console\Commands;

use App\Services\ListingService;
use Illuminate\Console\Command;

class ExpireListingsCommand extends Command
{
    protected $signature = 'listings:expire';

    protected $description = 'Soft delete active listings that have passed their expiration date';

    public function handle(ListingService $listingService): int
    {
        $count = $listingService->expireOverdueListings();

        $this->info("Expired {$count} listing(s).");

        return self::SUCCESS;
    }
}
