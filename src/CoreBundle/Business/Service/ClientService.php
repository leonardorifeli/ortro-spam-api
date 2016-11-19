<?php

namespace CoreBundle\Business\Service;

use CoreBundle\Business\Enum\ClientEnum;

class ClientService {

    private $googleClient;

    public function getGoogleClient()
    {
        if(!is_null($this->googleClient))
            return $this->googleClient;

        $this->googleClient = new \Google_Client();
        $this->googleClient->setApplicationName(ClientEnum::APPLICATION_NAME);
        $this->googleClient->setScopes(implode(' ', array(
                \Google_Service_Gmail::GMAIL_READONLY)
        ));
        $this->googleClient->setAuthConfig(ClientEnum::CLIENT_SECRET_PATH);
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setApprovalPrompt("force");

        return $this->googleClient;
    }

    public function get(string $accessToken)
    {
        if(!$accessToken)
            throw new \Exception("Access token invalid.", 401);

        $this->checkCredentialInformationIsValid($accessToken);

        return $this->getGoogleClient();
    }

    public function checkCredentialInformationIsValid(string $accessToken)
    {
        $this->getGoogleClient()->setAccessToken($accessToken);

        if (!$this->getGoogleClient()->isAccessTokenExpired())
            return;

        $this->getGoogleClient()->fetchAccessTokenWithRefreshToken($this->getGoogleClient()->getRefreshToken());

        return json_encode($this->getGoogleClient()->getAccessToken());
    }

}
