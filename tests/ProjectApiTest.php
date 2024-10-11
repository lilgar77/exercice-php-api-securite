<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectApiTest extends ApiTestCase
{

    /**
     * Test the GET /api/projects endpoint
     */
    public function testGetProjects(): void
    {
        $client = static::createClient();

        // Authenticate the user to access the projects
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host', // Replace with a valid user
                'password' => 'admin_password', // Replace with a valid password
            ],
        ]);

        // Check if the response contains a token
        $data = json_decode($response->getContent(), true);
        $token = $data['token'] ?? '';

        // Make a request to get the projects
        $response = $client->request('GET', '/api/projects', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseIsSuccessful();

        // Assert that the response contains projects
        $content = json_decode($response->getContent(), true);
        $this->assertNotEmpty($content['member']);
    }

    /**
     * Test the POST /api/projects endpoint
     */
    public function testCreateAndDeleteProject(): void
    {
        $client = static::createClient();

        // Authenticate the user to create a project
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        // Check if the response contains a token
        $data = json_decode($response->getContent(), true);
        $token = $data['token'] ?? '';

        // Prepare project data
        $projectData = [
            'title' => 'New Project',
            'description' => 'Description for the new project',
            'company' => '/api/companies/11',
        ];

        // Make a request to create a new project
        $response = $client->request('POST', '/api/projects', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json'
            ],
            'json' => $projectData,
        ]);

        // Assert the response status code is 201 Created
        $this->assertResponseStatusCodeSame(201);

        // Assert that the response contains the new project data
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content);
        $this->assertEquals('New Project', $content['title']);

        // Now delete the created project
        $projectId = $content['id'];
        $response = $client->request('DELETE', '/api/projects/' . $projectId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        // Assert that the deletion was successful
        $this->assertResponseStatusCodeSame(204); // No Content
    }

    /**
     * Test the PUT /api/projects/{id} endpoint
     */
    public function testUpdateProject(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'] ?? '';

        // Create a new project
        $client->request('POST', '/api/projects', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'title' => 'Project to Update',
                'description' => 'Initial Description',
            ],
        ]);

        // Retrieve the ID of the created project
        $projectResponse = $client->request('GET', '/api/projects', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
        ]);
        $projects = json_decode($projectResponse->getContent(), true);
        $projectId = $projects['member'][0]['id'];

        // Update the project using the PUT method
        $response = $client->request('PUT', '/api/projects/' . $projectId, [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => [
                'title' => 'Updated Project Title',
                'description' => 'Updated Description',
            ],
        ]);

        // Assert that the response status code is successful
        $this->assertResponseIsSuccessful();
        $updatedProject = json_decode($response->getContent(), true);
        $this->assertEquals('Updated Project Title', $updatedProject['title']);
        $this->assertEquals('Updated Description', $updatedProject['description']);
    }
}
