<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/api/projects', name: 'get_projects', methods: ['GET'])]
    public function getProjects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();
        return $this->json($projects);
    }

    #[Route('/api/projects/{id}', name: 'get_project', methods: ['GET'])]
    public function getProject(Project $project): Response
    {
        return $this->json($project);
    }

    #[Route('/api/projects', name: 'create_project', methods: ['POST'])]
    public function createProject(Request $request, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can add projects)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $project = new Project();
        $project->setTitle($data['title']);
        $project->setDescription($data['description']);
        $project->setCreatedAt(new \DateTime()); // Set creation date

        $em->persist($project);
        $em->flush();

        return $this->json($project, 201); // Return the project with a 201 Created status
    }

    #[Route('/api/projects/{id}', name: 'update_project', methods: ['PUT'])]
    public function updateProject(Request $request, Project $project, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can modify projects)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $project->setTitle($data['title'] ?? $project->getTitle());
        $project->setDescription($data['description'] ?? $project->getDescription());

        $em->flush();

        return $this->json($project);
    }

    #[Route('/api/projects/{id}', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProject(Project $project, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can delete projects)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $em->remove($project);
        $em->flush();

        return $this->json(null, 204); // Return a 204 No Content response
    }
}
