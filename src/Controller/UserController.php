<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/users/{id}', name: 'get_user', methods: ['GET'])]
    public function getUserById(User $user): Response
    {
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/api/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can add users)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);

        // Create a new user
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)); // Hash the password

        // Save the user
        $em->persist($user);
        $em->flush();

        // Return the user with a 201 Created status
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ], 201);
    }

    #[Route('/api/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can modify users)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        // Update the user
        $data = json_decode($request->getContent(), true);
        $user->setEmail($data['email'] ?? $user->getEmail());

        $em->flush();

        // Return the updated user
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can delete users)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        // Delete the user
        $em->remove($user);
        $em->flush();

        // Return a 204 No Content response
        return $this->json(null, 204);
    }


}
