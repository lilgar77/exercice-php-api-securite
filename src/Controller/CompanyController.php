<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;

use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[Route('/api/companies', name: 'get_companies', methods: ['GET'])]
    public function getCompanies(CompanyRepository $companyRepository): Response
    {
        $user = $this->getUser();
        $companies = $user->getCompanies();
        return $this->json($companies);
    }

    #[Route('/api/companies/{id}', name: 'get_company', methods: ['GET'])]
    public function getCompany(Company $company): Response
    {
        return $this->json($company);
    }

    #[Route('/api/companies', name: 'create_company', methods: ['POST'])]
    public function createCompany(Request $request, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can add companies)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        $data = json_decode($request->getContent(), true);
        // Create a new company
        $company = new Company();
        $company->setName($data['name']);
        $company->setSiret($data['siret']);
        $company->setAddress($data['address']);
        // Save the company
        $em->persist($company);
        $em->flush();

        return $this->json($company, 201); // Return the company with a 201 Created status
    }

    #[Route('/api/companies/{id}', name: 'update_company', methods: ['PUT'])]
    public function updateCompany(Request $request, Company $company, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can modify companies)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        // Update the company
        $data = json_decode($request->getContent(), true);
        $company->setName($data['name'] ?? $company->getName());
        $company->setSiret($data['siret'] ?? $company->getSiret());
        $company->setAddress($data['address'] ?? $company->getAddress());

        $em->flush();

        return $this->json($company);
    }

    #[Route('/api/companies/{id}', name: 'delete_company', methods: ['DELETE'])]
    public function deleteCompany(Company $company, EntityManagerInterface $em): Response
    {
        // Check access rights (only admins can delete companies)
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access Denied'], 403);
        }

        // Delete the company
        $em->remove($company);
        $em->flush();

        return $this->json(null, 204); // Return a 204 No Content response
    }
}
