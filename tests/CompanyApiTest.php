<?php

namespace App\Tests;


use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class CompanyApiTest extends ApiTestCase
{
    public function testGetCompanies(): void
    {
        // Create a client to make requests
        $client = static::createClient();

        // Request a JWT token for authentication
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        // Decode the response to get the token
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data, 'The JWT token was not found in the response.');
        $token = $data['token'];

        // Make a GET request to fetch the companies
        $response = $client->request('GET', '/api/companies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        // Retrieve the response content
        $content = $response->getContent();

        // Convert the content to an array and check for JSON errors
        $data = json_decode($content, true);
        $this->assertNotFalse($data, 'The JSON response is invalid: ' . json_last_error_msg());

        // Ensure the response contains the expected 'member' field
        $this->assertArrayHasKey('member', $data, 'The response does not contain the "member" field.');
        $this->assertIsArray($data['member'], 'The "member" field should be an array.');

        // Iterate through each company in the 'member' array
        foreach ($data['member'] as $company) {
            $this->assertIsArray($company, 'Each company should be an array.');
            $this->assertArrayHasKey('id', $company, 'Each company should have an ID.');
            $this->assertArrayHasKey('name', $company, 'Each company should have a name.');
            $this->assertArrayHasKey('siret', $company, 'Each company should have a SIRET.');
            $this->assertArrayHasKey('address', $company, 'Each company should have an address.');
        }
    }

    public function testGetCompanyById(): void
    {
        // Create a client to make requests
        $client = static::createClient();

        // Request a JWT token for authentication
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        // Decode the response to get the token
        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        // Assuming that we have a company with ID 1 already in the database
        $companyId = 11;

        // Make a GET request to fetch a specific company by ID
        $response = $client->request('GET', '/api/companies/' . $companyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        // Validate the response
        $this->assertResponseIsSuccessful();

        // Decode the response content
        $companyData = json_decode($response->getContent(), true);

        // Assert that the response contains the correct company details
        $this->assertArrayHasKey('id', $companyData, 'The company should have an ID.');
        $this->assertEquals($companyId, $companyData['id'], 'The returned company ID does not match the requested ID.');
        $this->assertArrayHasKey('name', $companyData, 'The company should have a name.');
        $this->assertArrayHasKey('siret', $companyData, 'The company should have a SIRET.');
        $this->assertArrayHasKey('address', $companyData, 'The company should have an address.');
    }

    public function testCreateAndDeleteCompany(): void
    {
        // Obtenir un jeton JWT en se connectant avec un utilisateur admin
        $client = static::createClient();
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        // Effectuer la requête de création de société
        $response = $client->request('POST', '/api/companies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/ld+json',
                'Accept' => 'application/ld+json',
            ],
            'json' => [
                'name' => 'Temporary Company',
                'siret' => '98765432109876',
                'address' => '456 Temporary Address',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($response->getContent(), true);
        $companyId = $data['id'];

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Temporary Company', $data['name']);
        $this->assertEquals('98765432109876', $data['siret']);
        $this->assertEquals('456 Temporary Address', $data['address']);

        $response = $client->request('DELETE', '/api/companies/' . $companyId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/ld+json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateCompany(): void
    {
        // Obtenir un jeton JWT en se connectant avec un utilisateur admin
        $client = static::createClient();
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        $response = $client->request('PUT', '/api/companies/11', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'name' => 'Updated Company Name',
                'siret' => '98765432101234',
                'address' => 'Updated Address',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('Updated Company Name', $data['name']);
        $this->assertEquals('98765432101234', $data['siret']);
        $this->assertEquals('Updated Address', $data['address']);
    }


}
