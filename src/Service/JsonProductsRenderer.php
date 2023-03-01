<?php

namespace App\Service;

class JsonProductsRenderer
{

  public function renderProducts(array $data): bool
  {
    try {
      echo json_encode($data);
    } catch (\Exception $e) {
      return false;
    }
    return true;
  }

}