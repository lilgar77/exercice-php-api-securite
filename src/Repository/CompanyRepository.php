<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * Récupérer les sociétés auxquelles un utilisateur appartient
     */
    public function findCompaniesByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.userRoles', 'ucr')
            ->where('ucr.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }
}
