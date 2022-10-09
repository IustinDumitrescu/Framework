<?php

namespace App\Service;

use App\Kernel;
use App\Utils\Utils;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

final class MediaUploadService
{
    private ?string $file;

    private ?string $directory;

    private int $fileSize;

    private string $root;

    private array $acceptType;

    private ?string $type;

    private int $requiredFileSize;

    private ?string $newFile;

    public function __construct(
        int $fileSize = 0,
        int $requiredFileSize = 0,
        ?string $file = null,
        ?string $directory = null,
        ?string $type = null,
        ?array $acceptType = [])
    {
        $this->root = Kernel::getRootDirectory();
        $this->file = $file;
        $this->directory = $directory;
        $this->fileSize = $fileSize;
        $this->type = $type;
        $this->acceptType = $acceptType;
        $this->requiredFileSize = $requiredFileSize;
    }


    public function uploadFile(): string
    {
        if (!file_exists($this->file)) {
            return 'Fisierul nu exista';
        }

        if (!is_dir($this->root .'/public/'. $this->directory)) {
            return 'Directorul nu exista';
        }

        if (empty($this->type) || empty($this->acceptType)
            || !in_array($this->type, $this->acceptType, true)) {
            return 'Tipul de fisier nu exista';
        }

        if ($this->fileSize > $this->requiredFileSize) {
            return 'Fisierul este prea mare';
        }

        $endStr = match ($this->type) {
            'image/jpeg' => '.jpeg',
            'image/jpg' => '.jpg',
            default => '.png',
        };

        $name = Utils::getRandomString(). $endStr;

        try {
           move_uploaded_file($this->file, $this->root .'public/'. $this->directory.'/'. $name);
           chmod($this->root .'public/'. $this->directory. "/" .$name, 0777);
        } catch (UploadException $exception) {
            $message = $exception->getMessage();
        }

        if (isset($message)) {
            return 'Fisierul nu a putut fi uploadat';
        }

        $this->newFile = $this->directory.'/'.$name;

        return 'success';
    }

    /**
     * @return string|null
     */
    public function getNewFile(): ?string
    {
        return $this->newFile;
    }


    /**
     * @param string $file
     * @return bool
     */
    public function deleteFile(string $file): bool
    {
        return unlink($file);
    }


}