<?php

declare(strict_types=1);

namespace App\Commands;

use App\View\View;
use Symfony\Component\Console\Command\HelpCommand as BaseHelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends BaseHelpCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandName = $input->getArgument('command');
        if ($commandName === 'help') {
            $commandName = $input->getArgument('command_name');
        }

        if (View::exists("help.{$commandName}")) {
            View::render("help.{$commandName}");

            return static::SUCCESS;
        }

        return parent::execute($input, $output);
    }
}
