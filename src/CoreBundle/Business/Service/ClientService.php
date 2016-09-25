<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Enum\ClientEnum;

class ClientService {

    private $googleClient;

    public function getGoogleClient()
    {
        if($this->googleClient) return $this->googleClient;

        $this->googleClient = new \Google_Client();
        $this->googleClient->setApplicationName(ClientEnum::APPLICATION_NAME);
        $this->googleClient->setScopes(implode(' ', array(
                \Google_Service_Gmail::GMAIL_READONLY)
        ));
        $this->googleClient->setAuthConfig(ClientEnum::CLIENT_SECRET_PATH);
        $this->googleClient->setAccessType('offline');

        return $this->googleClient;
    }

    public function get()
    {
        $credentialsPath = $this->getCredentialsPath();

        if(!file_exists($credentialsPath)) {
            throw new \Exception("Credentials file not exist.", 401);
        }

        $accessToken = json_decode(file_get_contents($credentialsPath), true);

        $this->getGoogleClient()->setAccessToken($accessToken);

        return $this->getGoogleClient();
    }

    public function getCredentialsPath() : string
    {
        return $this->expandHomeDirectory(ClientEnum::CREDENTIALS_PATH);
    }

    private function expandHomeDirectory($path) : string
    {
        $homeDirectory = getenv('HOME');

        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }

        return str_replace('~', realpath($homeDirectory), $path);
    }

}
