<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Récupérer les projets d'une société spécifique
     */
    public function findProjectsByCompany(int $companyId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.company = :company')
            ->setParameter('company', $companyId)
            ->getQuery()
            ->getResult();
    }
}
