<?php

namespace App\Service;

class ProductsFormatter
{
    /**
     * @return array
     */
    public function formatRows(array $array): array
    {
        $data = [];

        foreach($array as $values) {
            $row = [
                'Sku' => $values['sku'],
                'Status' => $values['is_enabled'] ? 'Enable' : 'Disable',
                'Price' => str_replace(".", ",", $values['price']) . $values['currency'],
                'Description'=> preg_replace("/(<br\W*?\/>)|\\\\r/", "\n", $values['description']),
                'Created At' => (new \Datetime($values['created_at'], new \DateTimeZone('CET')))->format('l, d-M-Y H:i:s e'),
                'Slug' =>strtolower(preg_replace('/\s+/', '_', preg_replace('/[^A-Za-z0-9 ]/', '-', str_replace(",", "", $values['title']) )))
            ];
            $data[] = $row;
        }
        return $data;
    }
}