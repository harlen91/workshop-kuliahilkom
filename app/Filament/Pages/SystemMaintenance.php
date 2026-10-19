<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class SystemMaintenance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?string $navigationLabel = 'Pemeliharaan Sistem';

    protected static ?string $title = 'Pemeliharaan Sistem & Database';

    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.system-maintenance';

    public ?string $lastCommand = null;

    public ?string $commandOutput = null;

    public ?string $executedAt = null;

    public bool $isSuccess = true;

    /**
     * Determine if user can access this page.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get storage symlink status.
     *
     * @return array{status: string, message: string, target: string, is_valid: bool}
     */
    public function getStorageStatus(): array
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        if (is_link($linkPath)) {
            $currentTarget = @readlink($linkPath) ?: '';
            $isValid = file_exists($linkPath);

            return [
                'status' => $isValid ? 'connected' : 'broken',
                'message' => $isValid ? 'Symlink terhubung aktif dan valid.' : 'Symlink rusak (target path tidak ditemukan).',
                'target' => $currentTarget,
                'is_valid' => $isValid,
            ];
        }

        if (is_dir($linkPath)) {
            return [
                'status' => 'directory',
                'message' => 'public/storage adalah folder biasa, bukan symlink.',
                'target' => $linkPath,
                'is_valid' => false,
            ];
        }

        return [
            'status' => 'not_linked',
            'message' => 'Symlink public/storage belum dibuat.',
            'target' => $targetPath,
            'is_valid' => false,
        ];
    }

    /**
     * Get system environment info.
     *
     * @return array<string, string>
     */
    public function getSystemInfo(): array
    {
        $dbConnection = (string) config('database.default', 'mysql');

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_env' => (string) app()->environment(),
            'db_connection' => $dbConnection,
            'db_host' => (string) config("database.connections.{$dbConnection}.host", '127.0.0.1'),
            'db_name' => (string) config("database.connections.{$dbConnection}.database", '-'),
            'storage_disk' => (string) config('filesystems.default', 'public'),
        ];
    }

    /**
     * Check migration status.
     */
    public function runMigrationStatus(): void
    {
        $this->executeArtisan('migrate:status', [], 'php artisan migrate:status');

        Notification::make()
            ->title('Status Migrasi Diperbarui')
            ->body('Hasil pengecekan status migrasi dapat dilihat pada konsol log.')
            ->info()
            ->send();
    }

    /**
     * Run database migrations.
     */
    public function runMigration(): void
    {
        $exitCode = $this->executeArtisan('migrate', ['--force' => true], 'php artisan migrate --force');

        if ($exitCode === 0) {
            Notification::make()
                ->title('Migrasi Database Berhasil')
                ->body('Skema database telah sinkron dan mutakhir.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Migrasi Gagal')
                ->body('Terjadi kesalahan saat mengeksekusi migrasi. Cek log output.')
                ->danger()
                ->send();
        }
    }

    /**
     * Run database seeder.
     */
    public function runDatabaseSeed(): void
    {
        $exitCode = $this->executeArtisan('db:seed', ['--force' => true], 'php artisan db:seed --force');

        if ($exitCode === 0) {
            Notification::make()
                ->title('Database Seeder Berhasil')
                ->body('Data awal telah berhasil dimasukkan ke dalam database.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Seeder Gagal')
                ->body('Terjadi kesalahan saat menjalankan seeder. Cek log output.')
                ->danger()
                ->send();
        }
    }

    /**
     * Create storage link.
     */
    public function runStorageLink(): void
    {
        $exitCode = $this->executeArtisan('storage:link', [], 'php artisan storage:link');

        if ($exitCode === 0) {
            Notification::make()
                ->title('Storage Link Berhasil')
                ->body('Direktori public/storage telah ditautkan.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Storage Link Informasi')
                ->body('Periksa konsol log untuk detail pesan.')
                ->warning()
                ->send();
        }
    }

    /**
     * Safely recreate/fix storage symlink.
     */
    public function runStorageRelink(): void
    {
        $linkPath = public_path('storage');

        try {
            if (is_link($linkPath) || file_exists($linkPath)) {
                @unlink($linkPath);
            }

            $exitCode = Artisan::call('storage:link');
            $output = "Membersihkan symlink lama di [{$linkPath}]...\n".Artisan::output();

            $this->commandOutput = $output;
            $this->lastCommand = 'Re-link Storage (unlink & php artisan storage:link)';
            $this->executedAt = now()->format('H:i:s, d M Y');
            $this->isSuccess = ($exitCode === 0);

            Notification::make()
                ->title('Storage Re-link Berhasil')
                ->body('Symlink public/storage telah diperbarui ke target direktori.')
                ->success()
                ->send();
        } catch (Throwable $e) {
            $this->commandOutput = "Gagal membuat ulang storage link: {$e->getMessage()}";
            $this->lastCommand = 'Re-link Storage (unlink & php artisan storage:link)';
            $this->executedAt = now()->format('H:i:s, d M Y');
            $this->isSuccess = false;

            Notification::make()
                ->title('Re-link Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Clear all application caches.
     */
    public function runOptimizeClear(): void
    {
        $this->executeArtisan('optimize:clear', [], 'php artisan optimize:clear');

        Notification::make()
            ->title('Cache Berhasil Dibersihkan')
            ->body('Cache konfigurasi, rute, view, dan event telah dikosongkan.')
            ->success()
            ->send();
    }

    /**
     * Optimize application cache.
     */
    public function runOptimize(): void
    {
        $this->executeArtisan('optimize', [], 'php artisan optimize');

        Notification::make()
            ->title('Aplikasi Berhasil Dioptimasi')
            ->body('File konfigurasi dan rute telah di-cache untuk performa maksimal.')
            ->success()
            ->send();
    }

    /**
     * Clear terminal console output.
     */
    public function clearOutput(): void
    {
        $this->commandOutput = null;
        $this->lastCommand = null;
        $this->executedAt = null;
    }

    /**
     * Helper to execute Artisan command safely and capture output.
     *
     * @param  array<string, mixed>  $parameters
     */
    protected function executeArtisan(string $command, array $parameters, string $displayCommand): int
    {
        try {
            $exitCode = Artisan::call($command, $parameters);
            $this->commandOutput = Artisan::output() ?: 'Perintah selesai dieksekusi tanpa pesan output.';
            $this->lastCommand = $displayCommand;
            $this->executedAt = now()->format('H:i:s, d M Y');
            $this->isSuccess = ($exitCode === 0);

            return $exitCode;
        } catch (Throwable $e) {
            $this->commandOutput = "Error: {$e->getMessage()}\n\n{$e->getTraceAsString()}";
            $this->lastCommand = $displayCommand;
            $this->executedAt = now()->format('H:i:s, d M Y');
            $this->isSuccess = false;

            return 1;
        }
    }
}
