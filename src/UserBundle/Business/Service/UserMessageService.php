<?php

namespace UserBundle\Business\Service;

use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use UserBundle\Business\Adapter\UserAdapter;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Business\Repository\UserMessageRepository;
use UserBundle\Business\Adapter\UserMessageAdapter;
use UserBundle\Entity\UserMessage;

class UserMessageService
{

    private $em;
    private $userService;

    public function __construct(EntityManager $em, UserService $userService)
    {
        $this->em = $em;
        $this->userService = $userService;
    }

    public function proccessHeaderMessageByUser(User $user, $messageId, $headers)
    {
        $entity = $this->getByProviderId($user, $messageId);

        if(!$entity) $entity = new UserMessage();

        UserMessageAdapter::buildHeader($entity, $user, $messageId, $headers);

        $this->em->persist($entity);

        return $entity;
    }

    public function flush()
    {
        $this->em->flush();
    }

    private function getByProviderId(User $user, $providerId)
    {
        return $this->getRepository()->findOneBy(['user' => $user->getId(), 'providerId' => $providerId]);
    }

    private function getRepository() : UserMessageRepository
    {
        return $this->em->getRepository("UserBundle:UserMessage");
    }

    private function getUserService()
    {
        return $this->userService;
    }
}
