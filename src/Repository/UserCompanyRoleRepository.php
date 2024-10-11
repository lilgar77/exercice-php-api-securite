<?php

namespace App\Repository;

use App\Entity\UserCompanyRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<UserCompanyRole>
 *
 * @method UserCompanyRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCompanyRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCompanyRole[]    findAll()
 * @method UserCompanyRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCompanyRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCompanyRole::class);
    }

    public function save(UserCompanyRole $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCompanyRole $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
