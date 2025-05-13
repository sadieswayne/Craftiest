<?php

namespace MagicAI\Updater\Commands;

use Illuminate\Console\Command;

class UpdaterCommand extends Command
{
    public $signature = 'magicai-updater';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
