<?php

namespace App\Models\Repository;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function getByPage($offset = 0, $limit = 3, $sortby = "id", $sorttype = "ASC")
    {
        $res = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('\App\Models\Task', 'p')
            ->orderBy('p.'.$sortby, $sorttype)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    
        return $res;
    }
}
