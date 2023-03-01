<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Service\DisplayCSVService;
use Monolog\DateTimeImmutable;

class DisplayCSV extends Command
{
    protected static $defaultName = 'app:display-csv';

    public function __construct(
        private $projectDir,  
        private DisplayCSVService $displayCSVService
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

        try {
            $this->displayCSVService->displayCsv($this->projectDir . $inputFile, $json);
         } catch (\Exception $e) {
            $output->writeLn($e->getMessage());
            return  Command::INVALID;
        }
        return Command::SUCCESS;
    }
}