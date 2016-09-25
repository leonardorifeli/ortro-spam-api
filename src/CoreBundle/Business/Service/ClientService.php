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

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    public function getCredentialsPath()
    {
        return $this->expandHomeDirectory(ClientEnum::CREDENTIALS_PATH);
    }

    private function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}
