<?php

namespace UserBundle\Business\Service;

use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Business\Adapter\UserAdapter;
use Symfony\Component\HttpFoundation\Response;
use CoreBundle\Business\Service\CredentialService;

class UserService
{

    private $em;
    private $credentialService;

    public function __construct(EntityManager $em, CredentialService $credentialService)
    {
        $this->em = $em;
        $this->credentialService = $credentialService;
    }

    private function getRepository()
    {
        return $this->em->getRepository("UserBundle:User");
    }

    public function createByRequest($info) : array
    {
        $this->validate($info);

        $user = UserAdapter::build($info, (new User()));

        return UserAdapter::toRequest($this->createOrUpdate($user));
    }

    public function userExist(string $email) : bool
    {
        $user = $this->getRepository()->findOneBy(["email" => $email]);

        if(!$user) return false;

        return true;
    }

    public function getUserByAccessToken(string $accessToken)
    {
        return $this->getRepository()->findOneBy(['accessToken' => $accessToken]);
    }

    public function updateUserByCredential($accessToken, $googleCode)
    {
        $this->validateTokenAndCode($accessToken, $googleCode);

        $user = $this->getUserByAccessToken($accessToken);
        if(!$user) throw new Exception("User not found", 404);

        $this->updateGoogleAccessTokenByUser($user, $this->credentialService->createCredential($googleCode));

        return UserAdapter::toRequest($user);
    }

    public function updateGoogleAccessTokenByUser(User $user, $googleAccessToken)
    {
        $user->setCredentialInformation($googleAccessToken);

        return $this->createOrUpdate($user);
    }

    public function getUserByEmailAndPasswordToRequest($info)
    {
        $this->validateSimpleRequest($info);

        $user = $this->getUserByEmailAndPassword($info->email, $info->password);

        if(!$user) return null;

        if($this->requireAuthorize($user)) return $this->credentialService->getAuthUrl();

        return UserAdapter::toRequest($user);
    }

    private function requireAuthorize(User $user) : bool
    {
        if($user->getCredentialInformation()) return false;

        return true;
    }

    public function getUserByEmailAndPassword(string $email, string $password)
    {
        $user = $this->getRepository()->findOneBy(["email" => $email, "password" => UserAdapter::encriptPassword($password)]);

        if(!$user) return null;

        return $user;
    }

    public function createOrUpdate(User $entity) : User
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    private function validate($info)
    {
        if(!property_exists($info, "name"))
            throw new \Exception("Name is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if(!property_exists($info, "email"))
            throw new \Exception("E-mail is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if((strlen($info->password) < 6) || !property_exists($info, "password"))
            throw new \Exception("Password is small.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if(!filter_var($info->email, FILTER_VALIDATE_EMAIL))
            throw new \Exception("E-mail is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if($this->userExist($info->email))
            throw new \Exception("User existis ({$info->email}).", Response::HTTP_UNAUTHORIZED);
    }

    private function validateTokenAndCode($token, $code)
    {
        if(is_null($token))
            throw new \Exception("Access token is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if(is_null($code))
            throw new \Exception("Google code is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function validateSimpleRequest($info)
    {
        if(!property_exists($info, "email"))
            throw new \Exception("E-mail is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if((strlen($info->password) < 6) || !property_exists($info, "password"))
            throw new \Exception("Password is small.", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
