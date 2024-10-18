<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['project:read']],
            security: 'is_granted("ROLE_USER")'
        ),
        new Post(
            denormalizationContext: ['groups' => ['project:write']],
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")'
        ),
        new Put(
            denormalizationContext: ['groups' => ['project:write']],
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")'
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")'
        )
    ],
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['project:read']],
    denormalizationContext: ['groups' => ['project:write']]
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['company:write', 'project:write', 'project:read'])]
    #[ApiProperty(
        openapiContext: [
            'example' => 'My Awesome Project'
        ]
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['company:write', 'project:write', 'project:read'])]
    #[ApiProperty(
        openapiContext: [
            'example' => 'This is a detailed description of the project'
        ]
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['project:read'])]
    #[ApiProperty(
        openapiContext: [
            'example' => '2024-10-17T14:00:00+00:00'
        ]
    )]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['project:read', 'project:write'])]
    #[ApiProperty(
        openapiContext: [
            'example' => '/api/companies/1'
        ]
    )]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Task::class, orphanRemoval: true)]
    #[Groups(['project:read'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        $this->tasks->removeElement($task);

        return $this;
    }
}
