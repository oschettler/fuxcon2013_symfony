<?php

namespace FUxCon2013\ProjectsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Entities;

class ProjectRepository extends EntityRepository
{


    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('
      SELECT COUNT(p.id)
      FROM FUxCon2013ProjectsBundle:Project p'
            )
            ->getSingleScalarResult();
    }

    public function findPaginated($offset, $size)
    {
        return $this->getEntityManager()
            ->createQuery('
      SELECT p.id, p.title, p.about
      FROM FUxCon2013ProjectsBundle:Project p
      ORDER BY p.title ASC'
            )
            ->setFirstResult($offset)
            ->setMaxResults($size)
            ->getResult();
    }
}
