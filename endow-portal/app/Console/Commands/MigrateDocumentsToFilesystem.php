<?php

namespace App\Console\Commands;

use App\Models\StudentDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MigrateDocumentsToFilesystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:migrate-to-filesystem {--limit=100 : Number of documents to migrate per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate documents from database (file_data) to filesystem (file_path) for better performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');

        $this->info('Starting migration of documents from database to filesystem...');

        // Get documents that have file_data but no file_path
        $documents = StudentDocument::whereNotNull('file_data')
            ->where(function($query) {
                $query->whereNull('file_path')
                    ->orWhere('file_path', '');
            })
            ->limit($limit)
            ->get();

        if ($documents->isEmpty()) {
            $this->info('✓ No documents found to migrate. All documents are already on filesystem!');
            return Command::SUCCESS;
        }

        $this->info("Found {$documents->count()} documents to migrate.");
        $progressBar = $this->output->createProgressBar($documents->count());
        $progressBar->start();

        $migrated = 0;
        $failed = 0;
        $errors = [];

        foreach ($documents as $document) {
            try {
                DB::beginTransaction();

                // Decode the base64 file data
                $fileContent = base64_decode($document->file_data);

                if (!$fileContent) {
                    throw new \Exception('Failed to decode file data');
                }

                // Generate sanitized filename
                $sanitizedFileName = $document->student_id . '_' . time() . '_' . uniqid() . '_' .
                    preg_replace('/[^a-zA-Z0-9._-]/', '_', $document->filename);

                // Save to filesystem
                $filePath = 'student-documents/' . $document->student_id . '/' . $sanitizedFileName;
                Storage::disk('public')->put($filePath, $fileContent);

                // Verify file was saved
                if (!Storage::disk('public')->exists($filePath)) {
                    throw new \Exception('File was not saved to filesystem');
                }

                // Update document record - keep file_data as backup for now
                $document->file_path = $filePath;
                $document->save();

                DB::commit();
                $migrated++;

            } catch (\Exception $e) {
                DB::rollBack();
                $failed++;
                $errors[] = "Document ID {$document->id}: " . $e->getMessage();

                $this->newLine();
                $this->error("✗ Failed to migrate document ID {$document->id}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info("Migration completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['✓ Successfully migrated', $migrated],
                ['✗ Failed', $failed],
                ['Total processed', $documents->count()],
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        // Check remaining documents
        $remaining = StudentDocument::whereNotNull('file_data')
            ->where(function($query) {
                $query->whereNull('file_path')
                    ->orWhere('file_path', '');
            })
            ->count();

        if ($remaining > 0) {
            $this->newLine();
            $this->warn("⚠ {$remaining} documents still need migration. Run this command again to continue.");
            $this->info("Command: php artisan documents:migrate-to-filesystem");
        } else {
            $this->newLine();
            $this->info('🎉 All documents have been migrated to filesystem!');
            $this->newLine();
            $this->comment('Optional: You can now remove file_data column from database to save space:');
            $this->comment('Run: php artisan make:migration remove_file_data_from_student_documents');
        }

        return Command::SUCCESS;
    }
}
