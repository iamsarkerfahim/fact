<?php
declare(strict_types=1);

namespace App\Services\FileHandler;

use App\Services\BaseService;

class FileHandler extends BaseService
{
    protected $filePath;

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
