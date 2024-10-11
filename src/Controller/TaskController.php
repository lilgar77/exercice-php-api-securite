<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/api/tasks', name: 'get_tasks', methods: ['GET'])]
    public function getTasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->json($tasks);
    }

    #[Route('/api/tasks/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(Task $task): Response
    {
        return $this->json($task);
    }

    #[Route('/api/tasks', name: 'create_task', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can add tasks)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Create a new task
        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setCreatedAt(new \DateTime()); // Set creation date



        $em->persist($task);
        $em->flush();

        return $this->json($task, 201); // Return the task with a 201 Created status
    }

    #[Route('/api/tasks/{id}', name: 'update_task', methods: ['PUT'])]
    public function updateTask(Request $request, Task $task, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can modify tasks)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $task->setTitle($data['title'] ?? $task->getTitle());
        $task->setDescription($data['description'] ?? $task->getDescription());

        $em->flush();

        return $this->json($task);
    }

    #[Route('/api/tasks/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(Task $task, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins and managers can delete tasks)
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_MANAGER')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $em->remove($task);
        $em->flush();

        return $this->json(null, 204); // Return a 204 No Content response
    }
}
