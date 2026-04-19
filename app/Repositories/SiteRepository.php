<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Site Settings and Contact Info
 */
class SiteRepository {
    private $dataPath;
    private $contactInfo = [];
    private $siteSettings = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_site.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->contactInfo = $contactInfo ?? [];
            $this->siteSettings = $siteSettings ?? [];
        }
    }

    public function getContactInfo(): array {
        return $this->contactInfo;
    }

    public function getSettings(): array {
        return $this->siteSettings;
    }

    public function saveContactInfo(array $data): bool {
        $this->contactInfo = array_merge($this->contactInfo, $data);
        return $this->save();
    }

    public function saveSettings(array $data): bool {
        $this->siteSettings = array_merge($this->siteSettings, $data);
        return $this->save();
    }

    private function save(): bool {
        $content = "<?php\n\n";
        $content .= '$contactInfo = ' . var_export($this->contactInfo, true) . ";\n\n";
        $content .= '$siteSettings = ' . var_export($this->siteSettings, true) . ";\n";
        
        $result = file_put_contents($this->dataPath, $content);
        if ($result !== false && function_exists('opcache_invalidate')) {
            @opcache_invalidate($this->dataPath, true);
        }
        return $result !== false;
    }
}
