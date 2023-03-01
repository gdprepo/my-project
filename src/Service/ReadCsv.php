<?php

namespace App\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class ReadCsv
{
    /**
     * @return array|false
     */
    public function decodeCsv(string $inputFile): mixed
    {
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $ext = pathinfo($inputFile, PATHINFO_EXTENSION);

        if (!file_exists($inputFile) || !filesize($inputFile) || $ext != "csv") {
            print('File not found or invalid');
            return false;
        }
        return $decoder->decode(file_get_contents($inputFile), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }
}