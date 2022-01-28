<?php

namespace IBS\Migration\SymfonyBundle;

use IBS\Migration\SymfonyBundle\Command\ConsoleCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IBSMigrationBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new ConsoleCommand());
    }
}
