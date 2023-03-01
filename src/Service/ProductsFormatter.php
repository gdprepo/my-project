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
            $datetime = explode(" ", $values['created_at']);
            $date = date_create($datetime[0]);

            $row = [
                'Sku' => $values['sku'],
                'Status' => $values['is_enabled'] ? 'Enable' : 'Disable',
                'Price' => str_replace(".", ",", $values['price']) . $values['currency'],
                'Description'=> preg_replace("/(<br\W*?\/>)|\\\\r/", "\n", $values['description']),
                'Created At' => date_format($date, 'l, d-M-Y') . ' ' . $datetime[1] . ' ' . $date->format('e'),
                'Slug' =>strtolower(preg_replace('/\s+/', '_', preg_replace('/[^A-Za-z0-9 ]/', '-', str_replace(",", "", $values['title']) )))
            ];
            $data[] = $row;
        }
        return $data;
    }
}