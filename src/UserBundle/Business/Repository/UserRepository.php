<?php

namespace UserBundle\Business\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findAllValidUsers() : array
    {
        $query = $this->createQueryBuilder('u')
                    ->where('u.isActive = true')
                    ->andWhere('u.credentialInformation IS NOT NULL');
        return $query->getQuery()->getResult();
    }

}
