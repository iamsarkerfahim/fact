<?php
declare(strict_types=1);

namespace App\Services\FileHandler;

use App\Services\BaseService;

abstract class FileHandler extends BaseService
{
    protected $filePath;

    protected abstract function getReader();

    /**
     * @throws \Exception
     */
    public function setFilePath(string $filePath): void
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new \Exception("Can not find the file '$filePath' or the file is not readable");
        }

        $this->filePath = $filePath;
    }
}
