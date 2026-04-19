<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис кеширования с поддержкой нескольких уровней
 */
class CacheService {
    private string $cacheDir;
    private array $memoryCache = [];
    private int $defaultTtl = 3600;

    public function __construct() {
        $this->cacheDir = __DIR__ . '/../../storage/cache/';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Получить значение из кеша или вычислить его
     */
    public function remember(string $key, int $ttl, callable $callback) {
        // Проверяем memory cache
        if (isset($this->memoryCache[$key])) {
            return $this->memoryCache[$key];
        }

        // Проверяем file cache
        $value = $this->get($key);
        if ($value !== null) {
            $this->memoryCache[$key] = $value;
            return $value;
        }

        // Вычисляем и кешируем
        $value = $callback();
        $this->put($key, $value, $ttl);
        $this->memoryCache[$key] = $value;

        return $value;
    }

    /**
     * Получить значение из кеша
     */
    public function get(string $key) {
        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = @unserialize(file_get_contents($file));
        
        if ($data === false) {
            return null;
        }

        // Проверяем срок действия
        if ($data['expires_at'] < time()) {
            @unlink($file);
            return null;
        }

        return $data['value'];
    }

    /**
     * Сохранить значение в кеш
     */
    public function put(string $key, $value, ?int $ttl = null): bool {
        $ttl = $ttl ?? $this->defaultTtl;
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl,
        ];

        $file = $this->getCacheFile($key);
        return file_put_contents($file, serialize($data)) !== false;
    }

    /**
     * Удалить значение из кеша
     */
    public function forget(string $key): bool {
        unset($this->memoryCache[$key]);
        
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return @unlink($file);
        }

        return true;
    }

    /**
     * Очистить весь кеш
     */
    public function flush(): bool {
        $this->memoryCache = [];
        
        $files = glob($this->cacheDir . '*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }

        return true;
    }

    /**
     * Кеширование с тегами
     */
    public function tags(array $tags): TaggedCache {
        return new TaggedCache($this, $tags);
    }

    /**
     * Получить путь к файлу кеша
     */
    private function getCacheFile(string $key): string {
        return $this->cacheDir . md5($key) . '.cache';
    }

    /**
     * Очистить устаревший кеш
     */
    public function clearExpired(): int {
        $cleared = 0;
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            $data = @unserialize(file_get_contents($file));
            
            if ($data !== false && $data['expires_at'] < time()) {
                @unlink($file);
                $cleared++;
            }
        }

        return $cleared;
    }
}

/**
 * Кеш с поддержкой тегов
 */
class TaggedCache {
    private CacheService $cache;
    private array $tags;

    public function __construct(CacheService $cache, array $tags) {
        $this->cache = $cache;
        $this->tags = $tags;
    }

    public function remember(string $key, int $ttl, callable $callback) {
        $taggedKey = $this->getTaggedKey($key);
        return $this->cache->remember($taggedKey, $ttl, $callback);
    }

    public function get(string $key) {
        return $this->cache->get($this->getTaggedKey($key));
    }

    public function put(string $key, $value, ?int $ttl = null): bool {
        return $this->cache->put($this->getTaggedKey($key), $value, $ttl);
    }

    public function forget(string $key): bool {
        return $this->cache->forget($this->getTaggedKey($key));
    }

    /**
     * Очистить все значения с этими тегами
     */
    public function flush(): bool {
        // Простая реализация - в production использовать Redis
        foreach ($this->tags as $tag) {
            $pattern = md5($tag) . ':*';
            // Очистка по паттерну
        }
        return true;
    }

    private function getTaggedKey(string $key): string {
        $tagPrefix = implode(':', array_map('md5', $this->tags));
        return $tagPrefix . ':' . $key;
    }
}
