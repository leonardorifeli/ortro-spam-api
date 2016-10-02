<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Service\ClientService;
use UserBundle\Business\Service\UserService;

class CredentialService {

    private $clientService;
    private $userService;

    public function __construct(UserService $userService, ClientService $clientService)
    {
        $this->userService = $userService;
        $this->clientService = $clientService;
    }

    public function getAuthUrl() : string
    {
        return $this->getGoogleClient()->createAuthUrl();
    }

    public function credentialIsExpired() : bool
    {
        return $this->getGoogleClient()->isAccessTokenExpired();
    }

    public function reloadAccessToken(User $user) : bool
    {
        if(!$this->credentialIsExpired()) return false;
        echo 1;die;
        dump($this->getGoogleClient()->getRefreshToken());die;

        $this->getGoogleClient()->fetchAccessTokenWithRefreshToken($this->getGoogleClient()->getRefreshToken());
        file_put_contents($this->getCredentialsPath(), json_encode($this->getGoogleClient()->getAccessToken()));

        return true;
    }

    public function get() : array
    {
        if(!$this->credentialIsExpired()) return json_decode(file_get_contents($credentialsPath), true);

        $this->reloadAccessToken();

        return json_decode(file_get_contents($credentialsPath), true);
    }

    public function createCredential(User $user, string $authCode) : string
    {
        if(!$authCode) throw new \Exception("Invalid auth code.", 401);

        $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($authCode);
        dump($accessToken);die;

        $this->getGoogleClient()->setAccessToken($accessToken);

        dump($accessToken);
        dump($this->reloadAccessToken());
        die;

        return json_encode($accessToken);
    }

    private function getGoogleClient()
    {
        return $this->clientService->getGoogleClient();
    }

    private function getUserService() : UserService
    {
        return $this->userService;
    }

}
