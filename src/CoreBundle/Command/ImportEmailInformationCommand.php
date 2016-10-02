<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CoreBundle\Business\Service\ClientService;
use UserBundle\Business\Service\UserService;

class ImportEmailInformationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('email:import')
            ->setDescription('Import email date informations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (php_sapi_name() != 'cli') {
            throw new \Exception('This application must be run on the command line.');
        }

        $users = $this->getUserService()->getAllValidUsers();

        array_map(function($user) {
            $client = $this->getClientService()->get($user->getCredentialInformation());

            //$service = new \Google_Service_Gmail($client);
            //$user = 'me';
            //$results = $service->users_labels->listUsersLabels($user);

            $service = new \Google_Service_Books($client);

            $client->setDefer(true);
            $optParams = array('filter' => 'free-ebooks');
            $request = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
            $resultsDeferred = $client->execute($request);

            dump($resultsDeferred);die;

        }, $users);

        $output->writeln('Command result.');
    }

    private function getUserService() : UserService
    {
        return $this->getContainer()->get('user.service');
    }

    private function getClientService() : ClientService
    {
        return $this->getContainer()->get('core.client.service');
    }

}
