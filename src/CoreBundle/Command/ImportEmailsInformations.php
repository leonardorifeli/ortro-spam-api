<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CoreBundle\Business\Service\ClientService;
use UserBundle\Business\Service\UserService;

class ImportEmailsInformations extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('email:proccess:import')
            ->setDescription('Import email date informations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (php_sapi_name() != 'cli') {
            throw new \Exception('This application must be run on the command line.');
        }

        $users = $this->getUserService()->getAllValidUsers();

        dump($users);die;

        $client = $this->getClientService();
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

    private function getUserService() : UserService
    {
        return $this->container->get('user.service');
    }

    private function getClientService() : ClientService
    {
        return $this->container->get('core.client.service');
    }

}
