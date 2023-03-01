<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Service\ReadCsv;
use App\Service\Products;
use Monolog\DateTimeImmutable;

class DisplayCSV extends Command
{
    protected static $defaultName = 'app:display-csv';

    public function __construct(
        private $projectDir, 
        private ReadCsv $readProductCsv,
        private Products $products,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Display CSV. CRON fréquence : 0 8 * * * cd [folder-root] && symfony console app:display-csv /public/products.csv')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addArgument('json', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getArgument('file'); 
        $json = $input->getArgument('json'); 

        // Convert csv file into array
        $array = $this->readProductCsv->decodeCsv($this->projectDir . $inputFile);
        // $array = $this->decodeCsv($inputFile);

        if ($array == false) {
            return Command::INVALID;
        }

        // Get  the values for set the rows
        $data = $this->products->formatRows($array);

        // Create and return the result table
        if ($json === "json") {
            echo json_encode($data);
            return Command::SUCCESS;
        }

        return $this->products->renderProducts($output, $data) ? Command::SUCCESS : Command::INVALID;
    }
}