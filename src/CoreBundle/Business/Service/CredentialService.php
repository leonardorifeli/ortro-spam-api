<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Service\ClientService;
use UserBundle\Business\Service\UserService;
use UserBundle\Entity\User;

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

    public function createCredential(User $user, string $authCode) : string
    {
        if(!$authCode)
            throw new \Exception("Invalid auth code.", 401);

        $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($authCode);

        $this->getGoogleClient()->setAccessToken($accessToken);

        return json_encode($accessToken);
    }

    public function checkCredentialInformationIsValid(User $user)
    {
        $this->checkCredentialToken($user);

        $token = $this->getClientService()->checkCredentialInformationIsValid($user->getCredentialInformation());

        if(!$token)
            return;

        $this->getUserService()->updateCredentialInformation($user, $token);
    }

    private function checkCredentialToken(User $user)
    {
        $token = json_decode($user->getCredentialInformation());

        if(!array_key_exists("expires_in", $token)) {
            $this->getUserService()->resetCredentialInformation($user);
            throw new \Exception("Credential information of user is invalid. Restart the login.", 500);
        }
    }

    private function getGoogleClient()
    {
        return $this->clientService->getGoogleClient();
    }

    private function getClientService()
    {
        return $this->clientService;
    }

    private function getUserService() : UserService
    {
        return $this->userService;
    }

}
