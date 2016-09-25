<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmailDateInformationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('email:import')
            ->setDescription('Import email date informations')
            ->addArgument('email', InputArgument::REQUIRED, 'Email of gmail');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('email');

        if (php_sapi_name() != 'cli') {
            throw new \Exception('This application must be run on the command line.');
        }

        $client = $this->getClient();
        $service = new \Google_Service_Gmail($client);

        $user = 'me';
        $results = $service->users_labels->listUsersLabels($user);

        if (count($results->getLabels()) == 0) {
            print "No labels found.\n";
        } else {
            print "Labels:\n";
            foreach ($results->getLabels() as $label) {
                printf("- %s\n", $label->getName());
            }
        }

        $output->writeln('Command result.');
    }

}
