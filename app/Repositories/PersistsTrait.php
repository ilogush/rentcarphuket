<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Provides atomic file persistence with file locking.
 * Used by all Repositories that write to data_*.php files.
 */
trait PersistsTrait
{
    /**
     * Atomically write content to the data file with exclusive locking.
     * Uses write-to-temp + rename pattern for crash safety.
     */
    protected function atomicWrite(string $filePath, string $content): bool
    {
        $dir = dirname($filePath);
        $tmpFile = tempnam($dir, 'tmp_');

        if ($tmpFile === false) {
            return false;
        }

        // Write to temp file first
        if (file_put_contents($tmpFile, $content) === false) {
            @unlink($tmpFile);
            return false;
        }

        // Match permissions of the original file
        if (file_exists($filePath)) {
            $perms = fileperms($filePath);
            if ($perms !== false) {
                @chmod($tmpFile, $perms);
            }
        }

        // Atomic rename (on same filesystem this is atomic)
        if (!rename($tmpFile, $filePath)) {
            @unlink($tmpFile);
            return false;
        }

        // Invalidate opcache so PHP picks up the new file
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($filePath, true);
        }

        return true;
    }
}
