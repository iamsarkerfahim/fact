<?php
declare(strict_types=1);

namespace App\Services\FileHandler;

use League\Csv\ByteSequence;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableFeature;

class CSVHandler extends FileHandler
{
    /**
     * @throws InvalidArgument
     * @throws UnavailableFeature
     * @throws Exception
     * @throws \Exception
     */
    public function getCSVRow(string $filePath, string $key, $value): ?array
    {
        $this->setFilePath($filePath);

        /** @var Reader $csvReader */
        $csvReader = Reader::createFromPath($this->filePath);
        $csvReader->setHeaderOffset(0);
        /** @var string $inputBom */
        $inputBom = $csvReader->getInputBOM();

        if ($inputBom === ByteSequence::BOM_UTF16_LE || $inputBom === ByteSequence::BOM_UTF16_BE) {
            $csvReader->addStreamFilter('convert.iconv.UTF-16/UTF-8');
        }

        foreach ($csvReader as $record) {
            if (is_array($record) && array_key_exists($key, $record) && (string)$record[$key] === (string)$value) {
                return $record;
            }
        }

        return null;
    }
}
