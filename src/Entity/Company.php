<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['company:read']],
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Post(
            denormalizationContext: ['groups' => ['company:write']],
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Put(
            denormalizationContext: ['groups' => ['company:write']],
            security: 'is_granted("ROLE_ADMIN")'
        ),
        new Delete(
            denormalizationContext: ['groups' => ['company:write']],
            security: 'is_granted("ROLE_ADMIN")'
        ),
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['company:read']],
    denormalizationContext: ['groups' => ['company:write']]
)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:write'])]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    #[Groups(['company:write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: UserCompanyRole::class, orphanRemoval: true)]
    private Collection $userRoles;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Project::class, orphanRemoval: true)]
    private Collection $projects;

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, UserCompanyRole>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserCompanyRole $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->setCompany($this);
        }

        return $this;
    }

    public function removeUserRole(UserCompanyRole $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            // Set the owning side to null (unless already changed)
            if ($userRole->getCompany() === $this) {
                $userRole->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setCompany($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // Set the owning side to null (unless already changed)
            if ($project->getCompany() === $this) {
                $project->setCompany(null);
            }
        }

        return $this;
    }
}
