<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ProjectApiTest extends ApiTestCase
{
    private string $token; // JWT token for authentication

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getToken();
    }

    private function getToken(): string
    {
        // Authenticate and get the JWT token
        $response = static::createClient()->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        return $response->toArray()['token'];
    }

    public function testGetByIdProjects(): void
    {
        // Test retrieving a project by ID
        $idProject = 46;
        $response = static::createClient()->request('GET', '/api/projects/' . $idProject, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testCreateAndDeleteProject()
    {
        // Test creating a project
        $client = static::createClient();
        $response = $client->request('POST', '/api/projects', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Project Title',
                'description' => 'Project Description',
                'company' => '/api/companies/81',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        // Test deleting the created project
        $projectId = $response->toArray()['id'];
        $response = $client->request('DELETE', '/api/projects/' . $projectId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateProject()
    {
        $client = static::createClient();

        // First, create a project to update
        $response = $client->request('POST', '/api/projects', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Original Project Title',
                'description' => 'Original Project Description',
                'company' => '/api/companies/81',
            ],
        ]);

        $projectId = $response->toArray()['id'];

        // Update the project
        $response = $client->request('PUT', '/api/projects/' . $projectId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Updated Project Title',
                'description' => 'Updated Project Description',
                'company' => '/api/companies/81',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);

        // Delete the updated project
        $response = $client->request('DELETE', '/api/projects/' . $projectId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}
