<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Service\ClientService;

class CredentialService {

    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    private function getCredentialsPath() :string
    {
        return $this->clientService->getCredentialsPath();
    }

    private function getGoogleClient()
    {
        return $this->clientService->getGoogleClient();
    }

    public function getAuthUrl() : string
    {
        return $this->getGoogleClient()->createAuthUrl();
    }

    public function credentialExist() : boolean
    {
        if(!file_exists($this->getCredentialsPath())) return false;

        return true;
    }

    public function credentialIsExpired() : boolean
    {
        if(!$this->credentialExist()) return true;

        return $this->getGoogleClient()->isAccessTokenExpired();
    }

    public function reloadAccessToken() : boolean
    {
        if(!$this->credentialIsExpired() || !$this->credentialExist()) return false;

        $this->getGoogleClient()->fetchAccessTokenWithRefreshToken($this->getGoogleClient()->getRefreshToken());
        file_put_contents($this->getCredentialsPath(), json_encode($this->getGoogleClient()->getAccessToken()));

        return true;
    }

    public function get() : string
    {
        if(!$this->credentialIsExpired()) return json_decode(file_get_contents($credentialsPath), true);

        $this->reloadAccessToken();

        return json_decode(file_get_contents($credentialsPath), true);
    }

    public function createCredential(string $authCode) : boolean
    {
        if(!$authCode) throw new \Exception("Invalid auth code.", 401);

        $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($authCode);

        $credentialsPath = $this->clientService->getCredentialsPath();
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }

        file_put_contents($credentialsPath, json_encode($accessToken));

        return true;
    }

}
