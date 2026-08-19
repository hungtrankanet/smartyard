<?php

namespace App\Services;

use Config\Globals;

/**
 * MediaStorageService: Decoupled Media Storage & CDN Resolution Layer
 * Provides unified abstraction across local persistent volume, AWS S3, and CDN delivery.
 */
class MediaStorageService
{
    protected string $storageDriver = 'local';
    protected string $uploadDir;
    protected ?string $cdnBaseUrl = null;
    protected $generalSettings;

    public function __construct(?string $driver = null)
    {
        $this->uploadDir = FCPATH . 'uploads/';
        $this->generalSettings = is_object(Globals::$generalSettings) ? Globals::$generalSettings : (object)[];

        if ($driver !== null) {
            $this->storageDriver = $driver;
        } elseif (!empty($this->generalSettings->storage) && $this->generalSettings->storage === 'aws_s3') {
            $this->storageDriver = 's3';
        } else {
            $this->storageDriver = env('MEDIA_STORAGE_DRIVER', 'local');
        }

        $envCdn = env('CDN_BASE_URL', null);
        if (!empty($envCdn)) {
            $this->cdnBaseUrl = rtrim($envCdn, '/');
        } elseif (!empty($this->generalSettings->cdn_url)) {
            $this->cdnBaseUrl = rtrim($this->generalSettings->cdn_url, '/');
        }
    }

    public function uploadFile($file, string $subfolder = 'media', ?string $customName = null): ?array
    {
        $subfolder = trim($subfolder, '/');
        $targetDir = $this->uploadDir . $subfolder . '/';

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        if (is_object($file) && method_exists($file, 'isValid') && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->guessExtension() ?: 'bin';
            $fileName = $customName ? $this->sanitizeFileName($customName) : 'media_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            
            $file->move($targetDir, $fileName);
            $relativePath = 'uploads/' . $subfolder . '/' . $fileName;

            if ($this->storageDriver === 's3') {
                $this->syncToS3($relativePath);
            }

            return [
                'file_name'     => $fileName,
                'relative_path' => $relativePath,
                'url'           => $this->getUrl($relativePath),
                'size'          => filesize($targetDir . $fileName),
                'storage'       => $this->storageDriver,
            ];
        }

        return null;
    }

    public function uploadFromBase64(string $base64Data, string $subfolder = 'media', string $ext = 'png', ?string $customName = null): ?array
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $ext = strtolower($type[1]);
        }

        $decoded = base64_decode($base64Data);
        if ($decoded === false) {
            return null;
        }

        $subfolder = trim($subfolder, '/');
        $targetDir = $this->uploadDir . $subfolder . '/';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $fileName = $customName ? $this->sanitizeFileName($customName) : 'b64_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $fullPath = $targetDir . $fileName;

        if (file_put_contents($fullPath, $decoded) !== false) {
            $relativePath = 'uploads/' . $subfolder . '/' . $fileName;
            if ($this->storageDriver === 's3') {
                $this->syncToS3($relativePath);
            }
            return [
                'file_name'     => $fileName,
                'relative_path' => $relativePath,
                'url'           => $this->getUrl($relativePath),
                'size'          => strlen($decoded),
                'storage'       => $this->storageDriver,
            ];
        }

        return null;
    }

    public function deleteFile(string $relativePath): bool
    {
        $relativePath = ltrim($relativePath, '/');
        $localPath = FCPATH . $relativePath;
        $deleted = false;

        if (file_exists($localPath)) {
            $deleted = @unlink($localPath);
        }

        if ($this->storageDriver === 's3') {
            try {
                $awsModel = new \App\Models\AwsModel();
                $awsModel->deleteFile($relativePath);
                $deleted = true;
            } catch (\Throwable $e) {
            }
        }

        return $deleted;
    }

    public function getUrl(?string $relativePath): string
    {
        if (empty($relativePath)) {
            return base_url('assets/img/icon_avatar.png');
        }

        if (filter_var($relativePath, FILTER_VALIDATE_URL)) {
            return $relativePath;
        }

        $relativePath = ltrim($relativePath, '/');

        if (!empty($this->cdnBaseUrl)) {
            return $this->cdnBaseUrl . '/' . $relativePath;
        }

        if ($this->storageDriver === 's3' && function_exists('getAWSBaseURL')) {
            $awsBase = getAWSBaseURL();
            if (!empty($awsBase)) {
                return rtrim($awsBase, '/') . '/' . $relativePath;
            }
        }

        return base_url($relativePath);
    }

    public function getAbsolutePath(string $relativePath): string
    {
        return FCPATH . ltrim($relativePath, '/');
    }

    protected function syncToS3(string $relativePath): void
    {
        try {
            $awsModel = new \App\Models\AwsModel();
            $awsModel->uploadFile($relativePath);
        } catch (\Throwable $e) {
        }
    }

    public function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
        return trim($name, '_');
    }

    public function getStorageDriver(): string
    {
        return $this->storageDriver;
    }

    public function isCloudStorage(): bool
    {
        return $this->storageDriver === 's3';
    }
}
