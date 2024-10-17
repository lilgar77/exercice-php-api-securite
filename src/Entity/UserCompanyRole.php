<?php

namespace App\Entity;

use App\Repository\UserCompanyRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;

#[ORM\Entity(repositoryClass: UserCompanyRoleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_ADMIN")',
            normalizationContext: ['groups' => ['user_company_role:read']]
        ),
        new Post(
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")',
            denormalizationContext: ['groups' => ['user_company_role:write']]
        ),
        new Put(
            security: 'is_granted("ROLE_ADMIN")',
            denormalizationContext: ['groups' => ['user_company_role:write']]
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")'
        )
    ],
    paginationEnabled: false
)]
class UserCompanyRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'userRoles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\Column(length: 50)]
    private ?string $role = null;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
}
