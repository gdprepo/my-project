<?php

namespace App\Service;

use App\Service\ReadCsv;
use App\Service\ProductsFormatter;
use App\Service\ConsoleProductsRenderer;
use App\Service\JsonProductsRenderer;

class DisplayCSVService
{
    public function __construct(
        private ReadCsv $readProductCsv,
        private ProductsFormatter $productsFormatter,
        private ConsoleProductsRenderer $consoleProductsRenderer,
        private JsonProductsRenderer $jsonProductsRenderer
    ) {}

    public function displayCSV(string $filename, ?string $json): bool
    {
      $array = $this->readProductCsv->decodeCsv($filename);
      $data = $this->productsFormatter->formatRows($array);

      if ($json === "json") {
          return $this->jsonProductsRenderer->renderProducts($data);
      }

      return $this->consoleProductsRenderer->renderProducts($data);
    }
}