<?php

namespace UserBundle\Business\Service;

use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Business\Adapter\UserAdapter;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Business\Repository\UserMessageRepository;

class UserMessageService
{

    private $em;
    private $userService;

    public function __construct(EntityManager $em, UserService $userService)
    {
        $this->em = $em;
        $this->userService = $userService;
    }

    private function getRepository() : UserRepository
    {
        return $this->em->getRepository("UserBundle:UserMessage");
    }

    private function getUserService()
    {
        return $this->userService;
    }
}
