<?php

namespace UserBundle\Business\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function getAllValidUsers() : array
    {
        $query = $this->createQueryBuilder('u')
                    ->where('u.isActive = true')
                    ->andWhere('u.credentialInformation <> null');

        return $query->getQuery()->getResult();
    }

}
