<?php
declare(strict_types=1);
namespace App\Services\Admin;

use App\Repositories\CarRepository;

class CarAdminService {
    private const MAX_IMAGE_BYTES = 5242880;
    private const MAX_IMAGE_DIMENSION = 1600;

    private CarRepository $repo;

    public function __construct(?CarRepository $repo = null) {
        $this->repo = $repo ?? new CarRepository();
    }

    public function getAll(): array {
        return $this->repo->getAll();
    }

    public function findById($id): ?array {
        return $this->repo->getById($id);
    }

    public function save(array $post, array $files = []): array {
        $imageResult = $this->resolveImage($files, $post);
        if (!$imageResult['success']) {
            return $imageResult;
        }

        $carData = [
            'id' => $post['car_id'] ?? '',
            'name' => $post['name'],
            'type' => $post['type'],
            'year' => (int)$post['year'],
            'transmission' => $post['transmission'],
            'seats' => (int)$post['seats'],
            'fuel' => $post['fuel'],
            'engine' => $post['engine'],
            'price' => (int)$post['price'],
            'deposit' => (int)($post['deposit'] ?? 0),
            'status' => $post['status'] ?? 'active',
        ];

        if (!empty($post['discount'])) {
            $carData['discount'] = (int)$post['discount'];
            $carData['discount_start'] = $post['discount_start'] ?? '';
            $carData['discount_end'] = $post['discount_end'] ?? '';
        } else {
            $carData['discount'] = '';
            $carData['discount_start'] = '';
            $carData['discount_end'] = '';
        }

        $carData['image'] = $imageResult['image'];
        $this->repo->save($carData);
        return ['success' => true, 'message' => 'Данные успешно сохранены!'];
    }

    public function toggleStatus($carId, $status): array {
        $car = $this->repo->getById($carId);
        if (!$car) {
            return ['success' => false, 'error' => 'Авто не найдено'];
        }
        $car['status'] = ($status === 'active') ? 'active' : 'inactive';
        $this->repo->save($car);
        return ['success' => true, 'message' => 'Статус обновлен'];
    }

    public function delete($carId): array {
        $this->repo->delete($carId);
        return ['success' => true, 'message' => 'Автомобиль удален!'];
    }

    private function resolveImage(array $files, array $post): array {
        if (!empty($files['image']['name'])) {
            $tmpName = $files['image']['tmp_name'] ?? '';
            if (!is_string($tmpName) || $tmpName === '' || !is_uploaded_file($tmpName)) {
                return ['success' => false, 'error' => 'Некорректная загрузка изображения'];
            }

            $fileSize = (int)($files['image']['size'] ?? 0);
            if ($fileSize <= 0 || $fileSize > self::MAX_IMAGE_BYTES) {
                return ['success' => false, 'error' => 'Размер изображения не должен превышать 5 МБ'];
            }

            $imageInfo = @getimagesize($tmpName);
            if ($imageInfo === false || empty($imageInfo['mime'])) {
                return ['success' => false, 'error' => 'Файл не является изображением'];
            }

            $mime = (string)$imageInfo['mime'];
            $allowedMimes = [
                'image/jpeg' => 'jpeg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];
            if (!isset($allowedMimes[$mime])) {
                return ['success' => false, 'error' => 'Разрешены только JPG, PNG и WEBP изображения'];
            }

            $normalized = $this->normalizeImage($tmpName, $mime, (int)$imageInfo[0], (int)$imageInfo[1]);
            if (!$normalized['success']) {
                return $normalized;
            }

            return ['success' => true, 'image' => $normalized['image']];
        }

        if (!empty($post['existing_image'])) {
            return ['success' => true, 'image' => $post['existing_image']];
        }

        return ['success' => true, 'image' => 'placeholder.webp'];
    }

    private function normalizeImage(string $sourcePath, string $mime, int $width, int $height): array
    {
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => false,
        };

        if ($image === false) {
            return ['success' => false, 'error' => 'Не удалось прочитать изображение'];
        }

        $targetWidth = $width;
        $targetHeight = $height;
        $maxDimension = self::MAX_IMAGE_DIMENSION;

        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = min($maxDimension / $width, $maxDimension / $height);
            $targetWidth = max(1, (int)round($width * $ratio));
            $targetHeight = max(1, (int)round($height * $ratio));
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($canvas === false) {
            imagedestroy($image);
            return ['success' => false, 'error' => 'Не удалось создать изображение'];
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);

        if (!imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height)) {
            imagedestroy($canvas);
            imagedestroy($image);
            return ['success' => false, 'error' => 'Не удалось обработать изображение'];
        }

        $uploadDir = app_image_storage_dir();
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            imagedestroy($canvas);
            imagedestroy($image);
            return ['success' => false, 'error' => 'Не удалось подготовить каталог изображений'];
        }

        $filename = uniqid('car_') . '.webp';
        $targetPath = $uploadDir . '/' . $filename;
        $saved = imagewebp($canvas, $targetPath, 85);

        imagedestroy($canvas);
        imagedestroy($image);

        if (!$saved) {
            return ['success' => false, 'error' => 'Не удалось сохранить изображение'];
        }

        return ['success' => true, 'image' => $filename];
    }
}
