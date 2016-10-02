<?php

namespace UserBundle\Business\Service;

use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Business\Adapter\UserAdapter;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Business\Service\CredentialService;

class UserCredentialService
{

    private $userService;
    private $credentialService;

    public function __construct(UserService $userService, CredentialService $credentialService)
    {
        $this->userService = $userService;
        $this->credentialService = $credentialService;
    }

    public function updateUserByCredential($accessToken, $googleCode)
    {
        $this->validateTokenAndCode($accessToken, $googleCode);

        $user = $this->getUserService()->getUserByAccessToken($accessToken);
        if(!$user) throw new Exception("User not found", 404);
    
        $googleAccessToken = $this->credentialService->createCredential($user, $googleCode);
        $this->updateGoogleAccessTokenByUser($user, $googleAccessToken);

        return UserAdapter::toRequest($user);
    }

    public function updateGoogleAccessTokenByUser(User $user, $googleAccessToken)
    {
        $user->setCredentialInformation(ltrim($googleAccessToken));

        return $this->getUserService()->createOrUpdate($user);
    }

    private function getUserService()
    {
        return $this->userService;
    }

    private function validateTokenAndCode($token, $code)
    {
        if(is_null($token))
            throw new \Exception("Access token is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if(is_null($code))
            throw new \Exception("Google code is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
