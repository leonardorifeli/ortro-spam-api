<?php

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CoreBundle\Business\Service\ClientService;
use UserBundle\Business\Service\UserService;
use UserBundle\Entity\User;
use UserBundle\Business\Service\UserMessageService;

class ImportEmailInformationCommand extends ContainerAwareCommand
{

    const LIMIT = 200;
    private $gmailService;
    private $userCredential;

    protected function configure()
    {
        $this->setName('email:import')->setDescription('Import email with some informations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (php_sapi_name() != 'cli')
            throw new \Exception('This application must be run on the command line.');

        array_map(function($user) use ($output) {
            $this->proccessUserCredential($user->getCredentialInformation());
            $this->proccessMessages($user, $output);
        }, $this->getUserService()->getAllValidUsers());

        $output->writeln("\n<info>Finished all!</info>");
    }

    private function proccessMessages(User $user, OutputInterface $output)
    {
        $messages = $this->getMessages($user, self::LIMIT, "UNREAD");

        $i = 0;
        $actual = 1;
        $countList = $this->getCountList($messages);
        while($i <= $countList) {
            if($i != $countList) 
                $output->write("<info>Importing {$actual} message...</info>"); 

            $this->proccessSingleMessage($user, $messages[$i]);

            if($i != $countList)
                $output->writeln(" Finished!");

            if($i == $countList) {
                if(is_null($messages->nextPageToken))
                    break;

                $this->proccessListMoreMessage($user, $i, $messages);

                if(count($messages) == 0)
                    break;

                $this->getUserMessageService()->flush();
            }

            $this->getUserMessageService()->flush();

            $i++;
            $actual++;
        }

        return;
    }

    private function proccessListMoreMessage(User $user, int &$i, &$messages)
    {
        $i = 0;
        $messages = $this->getMessages($user, self::LIMIT, "UNREAD", $messages->nextPageToken);
        return;
    }

    private function proccessSingleMessage(User $user, $message)
    {
        $optParamsGet['format'] = 'full';
        $message = $this->getGmailService()->users_messages->get('me', $message->id, $optParamsGet);

        $headers = $message->getPayload()->getHeaders();

        $this->getUserMessageService()->proccessHeaderMessageByUser($user, $message->id, $headers);

        unset($message);
    }

    private function proccessBodyMessage($message)
    {
        $parts = $message->getPayload()->getParts();

        if(count($parts) < 1) return;

        $body = $parts[0]['body'];

        $rawData = $body->data;
        $sanitizedData = strtr($rawData,'-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);
    }

    private function getMessages(User $user, int $limit, string $label, string $pageToken = "") : \Google_Service_Gmail_ListMessagesResponse
    {
        $optParams['maxResults'] = $limit;
        $optParams['labelIds'] = $label;

        if($pageToken)
            $optParams['pageToken'] = $pageToken;

        return $this->getGmailService()->users_messages->listUsersMessages("me", $optParams);
    }

    private function getGmailService()
    {
        if($this->gmailService)
            return $this->gmailService;

        $this->gmailService = new \Google_Service_Gmail($this->getClient());

        return $this->gmailService;
    }

    private function proccessUserCredential(string $credential)
    {
        $this->userCredential = $credential;
    }

    private function getUserCredential()
    {
        return $this->userCredential;
    }

    private function getClient()
    {
        return $this->getClientService()->get($this->getUserCredential());
    }

    private function getUserService() : UserService
    {
        return $this->getContainer()->get('user.service');
    }

    private function getClientService() : ClientService
    {
        return $this->getContainer()->get('core.client.service');
    }

    private function getUserMessageService() : UserMessageService
    {
        return $this->getContainer()->get('user.message.service');
    }

    private function getCountList($array)
    {
        return count($array) - 1;
    }

}
