<?php

namespace App\Service;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleProductsRenderer 
{
    private ConsoleOutput $output;

    public function __construct() 
    {
        $this->output = new ConsoleOutput;
    }
    /**
     * @return true|false
     */
    public function renderProducts(array $data): bool
    {
        $table = new Table($this->output);
        $table
            ->setHeaders(["Sku", "Status", "Price", "Description", "Created At", "Slug"])
            ->setRows($data)
        ;

        try {
            $table->render();
        } catch (\Exception $e) {
            print($e->getMessage());
            return false;
        }

        return true;
    }
}