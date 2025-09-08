<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestAwsConnection extends Command
{
    protected $signature = 'aws:test';
    protected $description = 'Test AWS S3 connection and file upload capabilities';

    public function handle(): int
    {
        $this->info('Starting AWS S3 connection test...');
        $appPath = config('services.aws.root', '');

        try {
            $this->info('1. Attempting to upload a test file...');
            $testContent = 'This is a test file created at ' . now();
            $testFilePath = $appPath .'/aws-test-' . time() . '.txt';

            $success = Storage::disk('s3')->put($testFilePath, $testContent);

            if ($success) {
                $this->info('✓ Successfully uploaded test file to: ' . $testFilePath);
                $this->info('Debugging information:');


                $this->info('2. All directories in root:');
                $directories = Storage::disk('s3')->directories('');
                foreach ($directories as $dir) {
                    $this->line('- ' . $dir);
                }

                $this->info('3. All files in root:');
                $rootFiles = Storage::disk('s3')->files('');
                foreach ($rootFiles as $file) {
                    $this->line('- ' . $file);
                }

                $this->info('4. All files in app directory:');
                $testFiles = Storage::disk('s3')->files($appPath);
                foreach ($testFiles as $file) {
                    $this->line('- ' . $file);
                }

                $this->info('5. File exists check:');
                $exists = Storage::disk('s3')->exists($testFilePath);
                $this->line("File $testFilePath exists: " . ($exists ? 'Yes' : 'No'));

                $this->info('6. Full URL to file:');
                $url = Storage::disk('s3')->url($testFilePath);
                $this->line($url);

                $this->info('7. Attempting to read the uploaded file...');
                $readContent = Storage::disk('s3')->get($testFilePath);

                if ($readContent === $testContent) {
                    $this->info('✓ Successfully read the test file');
                } else {
                    $this->error('× File content verification failed');
                }

                // Test 4: Clean up - Delete the test file
                $this->info('8. Cleaning up - Deleting test file...');
                if (Storage::disk('s3')->delete($testFilePath)) {
                    $this->info('✓ Successfully deleted test file');
                } else {
                    $this->error('× Failed to delete test file');
                }
            } else {
                $this->error('× Failed to upload test file');
            }

        } catch (Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            $this->line('Stack trace:');
            $this->line($e->getTraceAsString());
            return 1;
        }

        $this->info('AWS S3 test completed!');
        return 0;
    }
}
