<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class CompanyApiTest extends ApiTestCase
{
    private string $token; // JWT token for authentication

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getToken();
    }

    private function getToken(): string
    {
        // Authenticate and retrieve the JWT token
        $response = static::createClient()->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        return $response->toArray()['token']; // Return the token
    }

    public function testCreateAndDeleteCompany(): void
    {
        // Create a new company
        $response = $this->createClient()->request('POST', '/api/companies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'siret' => '44488989898989',
                'address' => '123 Company_test',
                'name' => 'Test Company',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = $response->toArray();
        $companyId = (int) preg_replace('/\/api\/companies\//', '', $data['@id']); // Extract company ID

        $this->assertNotNull($companyId, 'The created company ID is null.'); // Check ID is not null

        // Delete the created company
        $response = static::createClient()->request('DELETE', '/api/companies/' . $companyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testGetCompaniesById(): void
    {
        $companyId = 82; // ID of an existing company

        // Retrieve the company by ID
        $response = $this->createClient()->request('GET', '/api/companies/' . $companyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testUpdateCompany(): void
    {
        $companyId = 82;

        // Update the company
        $response = $this->createClient()->request('PUT', '/api/companies/' . $companyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'siret' => '44488989898989',
                'address' => '123 Company_test',
                'name' => 'Test Company',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }
}
