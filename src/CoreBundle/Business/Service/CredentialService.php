<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Service\ClientService;

class CredentialService {

    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    private function getGoogleClient()
    {
        return $this->clientService->getGoogleClient();
    }

    public function getAuthUrl()
    {
        return $this->getGoogleClient()->createAuthUrl();
    }

    public function createCredential($authCode, $email)
    {
        if(!$authCode) throw new \Exception("Invalid auth code." 401);

        $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($authCode);

        $credentialsPath = $this->clientService->getCredentialsPath();
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }

        file_put_contents($credentialsPath, json_encode($accessToken));

        return true;
    }

}
