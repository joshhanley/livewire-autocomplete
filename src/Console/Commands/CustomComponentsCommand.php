<?php

namespace LivewireAutocomplete\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomComponentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocomplete:custom-components';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate custom components for Livewire autocomplete to implement custom styles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packageBasePath = __DIR__.'/../../../';
        $sourcePath = $packageBasePath.'stubs/custom-components/autocomplete';
        $destinationPath = 'views/components/autocomplete';
        $fullDestinationPath = resource_path($destinationPath);

        $this->info('Generating custom components...');

        if (! File::exists($sourcePath)) {
            $this->error('Source directory does not exist.');

            return 1;
        }

        if (! File::exists($fullDestinationPath)) {
            File::makeDirectory($fullDestinationPath, 0755, true);
        }

        $files = File::allFiles($sourcePath);

        foreach ($files as $file) {
            $destinationFile = $fullDestinationPath.'/'.$file->getFilename();
            File::copy($file->getPathname(), $destinationFile);
            $this->info("Generated: {$destinationPath}/{$file->getFilename()}");
        }

        $this->info('All custom components have been generated.');

        return 0;
    }
}
