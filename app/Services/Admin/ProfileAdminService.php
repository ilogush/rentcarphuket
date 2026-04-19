<?php
declare(strict_types=1);

namespace App\Services\Admin;

class ProfileAdminService {
    public function save(array $post): array {
        $siteRepo = new \App\Repositories\SiteRepository();
        
        // Validate required fields
        if (empty($post['name']) || empty($post['email']) || empty($post['phone']) || empty($post['address'])) {
            return ['success' => false, 'error' => 'Все обязательные поля должны быть заполнены'];
        }

        // Validate email
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Некорректный email адрес'];
        }

        // Handle password change
        if (!empty($post['new_password']) || !empty($post['confirm_password'])) {
            if ($post['new_password'] !== $post['confirm_password']) {
                return ['success' => false, 'error' => 'Пароли не совпадают'];
            }
            
            if (strlen($post['new_password']) < 6) {
                return ['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов'];
            }

            // Update password in .env file
            $this->updateEnvFile([
                'ADMIN_PASSWORD_HASH' => password_hash($post['new_password'], PASSWORD_DEFAULT),
            ]);
        }

        // Update admin name in .env and session
        $envUpdated = $this->updateEnvFile([
            'ADMIN_NAME' => $post['name'],
        ]);
        
        if (!$envUpdated) {
            return ['success' => false, 'error' => 'Не удалось обновить имя администратора'];
        }
        
        if (isset($_SESSION['admin_name'])) {
            $_SESSION['admin_name'] = $post['name'];
        }
        
        // Update email in session
        if (isset($_SESSION['admin_email'])) {
            $_SESSION['admin_email'] = $post['email'];
        }
        
        // Update contact info
        $contactSaved = $siteRepo->saveContactInfo([
            'phone' => $post['phone'],
            'email' => $post['email'],
            'address' => $post['address'],
            'socialMedia' => array_merge(
                $siteRepo->getContactInfo()['socialMedia'] ?? [],
                ['telegram' => $post['telegram'] ?? '']
            ),
        ]);

        if (!$contactSaved) {
            return ['success' => false, 'error' => 'Не удалось сохранить контактные данные'];
        }

        return ['success' => true, 'message' => 'Профиль и контактные данные успешно обновлены'];
    }

    public function getProfileData(array $session): array {
        $siteRepo = new \App\Repositories\SiteRepository();
        $contact = $siteRepo->getContactInfo();

        return [
            'name' => $session['admin_name'] ?? 'Admin',
            'role' => $session['admin_role'] ?? 'Администратор',
            'email' => $contact['email'] ?? 'admin@rentcarphuket.ru',
            'phone' => $contact['phone'] ?? '',
            'telegram' => $contact['socialMedia']['telegram'] ?? '',
            'address' => $contact['address'] ?? '',
            'username' => 'admin',
        ];
    }

    private function updateEnvFile(array $updates): bool {
        $envPath = __DIR__ . '/../../../.env';
        
        if (!file_exists($envPath)) {
            return false;
        }

        $envContent = file_get_contents($envPath);
        if ($envContent === false) {
            return false;
        }

        foreach ($updates as $key => $value) {
            if (!preg_match('/^[A-Z0-9_]+$/', (string)$key)) {
                return false;
            }

            $line = $key . '=' . $this->formatEnvValue((string)$value);
            $pattern = '/^' . preg_quote((string)$key, '/') . '=.*$/m';
            $lines = preg_split('/\R/', rtrim($envContent, "\r\n"));
            $replaced = false;

            foreach ($lines as &$existingLine) {
                if (preg_match($pattern, $existingLine)) {
                    $existingLine = $line;
                    $replaced = true;
                    break;
                }
            }
            unset($existingLine);

            if (!$replaced) {
                $lines[] = $line;
            }

            $envContent = implode("\n", $lines) . "\n";
        }

        $result = $this->atomicWrite($envPath, $envContent);

        if ($result && function_exists('opcache_invalidate')) {
            @opcache_invalidate($envPath, true);
        }

        return $result;
    }

    private function formatEnvValue(string $value): string {
        if (preg_match('/^[A-Za-z0-9_@.%+\/:$-]*$/', $value)) {
            return $value;
        }

        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    private function atomicWrite(string $path, string $content): bool {
        $dir = dirname($path);
        $tmpPath = tempnam($dir, '.env_tmp_');

        if ($tmpPath === false) {
            return false;
        }

        if (file_put_contents($tmpPath, $content, LOCK_EX) === false) {
            @unlink($tmpPath);
            return false;
        }

        $perms = file_exists($path) ? fileperms($path) : false;
        if ($perms !== false) {
            @chmod($tmpPath, $perms);
        }

        if (!rename($tmpPath, $path)) {
            @unlink($tmpPath);
            return false;
        }

        return true;
    }
}
