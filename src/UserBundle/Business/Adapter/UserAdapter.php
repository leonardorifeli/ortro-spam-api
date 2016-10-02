<?php

namespace UserBundle\Business\Adapter;

use UserBundle\Entity\User;

abstract class UserAdapter
{

    public static final function build ($information, User $entity) : User
    {
        $entity->setName($information->name);
        $entity->setEmail($information->email);
        $entity->setAccessToken(self::encriptAccessToken());
        $entity->setPassword(self::encriptPassword($information->password));
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
        $entity->setIsActive(true);

        return $entity;
    }

    public static final function encriptPassword(string $password) : string
    {
        return hash('sha256', md5($password.md5($password)));
    }

    private static final function encriptAccessToken() : string
    {
        return hash('sha256', md5(uniqid(rand(), true)));
    }

    public static final function toRequest(User $entity)
    {
        return $data = [
            "name" => $entity->getName(),
            "email" => $entity->getEmail(),
            "token" => $entity->getAccessToken(),
            'oauth2' => json_decode($entity->getCredentialInformation())
        ];
    }

}
