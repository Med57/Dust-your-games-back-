<?php

namespace App\Command;

use App\Service\UpdateWeightService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddOneMonthly extends Command
{
    
    private $update;
    protected static $defaultName = 'add:monthly';


    public function __construct(UpdateWeightService $update){

        parent::__construct();
        $this->update = $update;

    }

    public function execute(InputInterface $input, OutputInterface $output){

        $this->update->updateByMonth();
        return Command::SUCCESS;

    }


}