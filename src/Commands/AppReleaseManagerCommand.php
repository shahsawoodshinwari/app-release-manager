<?php

namespace Shaka\AppReleaseManager\Commands;

use Illuminate\Console\Command;
use Shaka\AppReleaseManager\Database\Seeders\ReferenceDataSeeder;

class AppReleaseManagerCommand extends Command
{
    public $signature = 'arm:seed-reference-data';

    public $description = 'Seed the package reference data (platforms, channels, release types, statuses)';

    public function handle(): int
    {
        $this->call(ReferenceDataSeeder::class);

        $this->comment('Reference data seeded.');

        return self::SUCCESS;
    }
}
