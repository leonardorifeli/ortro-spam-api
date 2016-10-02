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

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function getRepository() : UserRepository
    {
        return $this->em->getRepository("UserBundle:UserMessage");
    }

}
