<?php

namespace UserBundle\Business\Service;

use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Business\Adapter\UserAdapter;
use Symfony\Component\HttpFoundation\Response;

class UserService
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function getRepository()
    {
        return $this->em->getRepository("UserBundle:User");
    }

    public function createByRequest($info) : array
    {
        $this->validate($info);

        $user = UserAdapter::build($info, (new User()));

        return UserAdapter::toRequest($this->create($user));
    }

    public function userExist(string $email) : bool
    {
        $user = $this->getRepository()->findOneBy(["email" => $email]);

        if(!$user) return false;

        return true;
    }

    private function validate($info)
    {
        if(!property_exists($info, "name"))
            throw new \Exception("Name is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if(!property_exists($info, "email"))
            throw new \Exception("E-mail is invalid.", Response::HTTP_INTERNAL_SERVER_ERROR);

        if($this->userExist($info->email))
            throw new \Exception("User existis ({$info->email}).", Response::HTTP_UNAUTHORIZED);
    }

    public function create(User $entity) : User
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

}
