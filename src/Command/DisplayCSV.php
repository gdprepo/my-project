<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Validator\Constraints\DateTime;
use Monolog\DateTimeImmutable;

class DisplayCSV extends Command
{
    protected static $defaultName = 'app:display-csv';

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Display CSV. CRON fréquence : 0 8 * * * cd [folder-root] && symfony console app:display-csv /public/products.csv')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addArgument('json', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('file'); 
        $json = $input->getArgument('json'); 

        // Convert csv file into array
        $array = $this->decodeCsv($inputFile);

        if ($array == false) {
            return Command::INVALID;
        }

        // Get  the values for set the rows
        $data = $this->formatRows($array);

        // Create and return the result table
        if ($json === "json") {
            echo json_encode($data);
            return Command::SUCCESS;
        }

        return $this->renderProducts($output, $data) ? Command::SUCCESS : Command::INVALID;
    }

    /**
     * @return true|false
     */
    public function renderProducts(OutputInterface $output, array $data): bool
    {
        $table = new Table($output);
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

    /**
     * @return array|false
     */
    public function decodeCsv(string $paramFile): mixed
    {
        $inputFile = $this->projectDir . $paramFile;
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $ext = pathinfo($inputFile, PATHINFO_EXTENSION);

        if (!file_exists($inputFile) || !filesize($inputFile) || $ext != "csv") {
            print('File not found or invalid');
            return false;
        }
        return $decoder->decode(file_get_contents($inputFile), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);
    }
}