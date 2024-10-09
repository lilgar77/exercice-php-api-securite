<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserApiTest extends ApiTestCase
{
    public function testGetUsers(): void
    {
        // Create the client to make requests
        $client = static::createClient();

        // Make a GET request to the /api/users endpoint
        $response = $client->request('GET', '/api/users');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the content is not empty
        $this->assertNotEmpty($response->getContent());

        // Convert the JSON response to an array
        $data = json_decode($response->getContent(), true);

        // Assert that the data has the expected structure
        foreach ($data as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('email', $user);
        }
    }

    public function testGetUserById(): void
    {
        // Create the client to make requests
        $client = static::createClient();

        // Assuming user with ID 1 exists in the database
        $response = $client->request('GET', '/api/users/3');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Convert the JSON response to an array
        $data = json_decode($response->getContent(), true);

        // Assert that the data has the expected structure
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('email', $data);
    }

   /* public function testCreateUser(): void
    {
        // Create the client to make requests
        $client = static::createClient();

        // Make a POST request to the /api/users endpoint
        $response = $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'newuser@local.host',
                'password' => 'newpassword123',
            ],
        ]);

        // Assert that the user was created successfully
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);

        // Assert that the response contains the new user's data
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('newuser@local.host', $data['email']);
    }

    public function testUpdateUser(): void
    {
        // Create the client to make requests
        $client = static::createClient();

        // Make a PUT request to the /api/users/1 endpoint
        $response = $client->request('PUT', '/api/users/3', [
            'json' => [
                'email' => 'updateduser@local.host',
            ],
        ]);

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);

        // Assert that the updated user's data is correct
        $this->assertEquals('updateduser@local.host', $data['email']);
    }

    public function testDeleteUser(): void
    {
        // Create the client to make requests
        $client = static::createClient();

        // Make a DELETE request to the /api/users/1 endpoint
        $client->request('DELETE', '/api/users/5');

        // Assert that the response indicates successful deletion
        $this->assertResponseStatusCodeSame(204);

        // Optionally, verify that the user no longer exists
        $response = $client->request('GET', '/api/users/4');
        $this->assertResponseStatusCodeSame(404); // Assuming user not found returns 404
    }*/
}
