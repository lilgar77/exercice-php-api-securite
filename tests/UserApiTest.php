<?php

namespace App\Tests;

use App\Entity\User;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserApiTest extends ApiTestCase
{
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Authenticate as admin to get the JWT token for further requests
        $response = $this->createClient()->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password'
            ]
        ]);

        $data = $response->toArray();
        // Safely set the token, defaulting to an empty string if not found
        $this->token = $data['token'] ?? '';
    }

    public function testGetUserById()
    {
        $userId = 232;

        // Fetch the user by ID
        $response = $this->createClient()->request('GET', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);

        // Verify the response is successful and in JSON format
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($response->getContent());

        $user = $response->toArray();
        // Ensure the user data contains the expected ID
        $this->assertArrayHasKey('id', $user);
        $this->assertEquals($userId, $user['id']);
    }

    public function testCreateAndDeleteUser(): void
    {
        // Get the JWT token
        $response = static::createClient()->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $token = $response->toArray()['token'];

        // Create a new user
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'email' => 'testuser444@example.com',
                'password' => 'securepassword',
                'roles' => ['ROLE_USER'],
            ],
        ]);

        // Check if user creation was successful
        $this->assertResponseStatusCodeSame(201);

        $userId = $response->toArray()['id'];

        // Delete the created user
        $response = static::createClient()->request('DELETE', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        // Assert successful deletion
        $this->assertResponseStatusCodeSame(204);
    }

    public function testUpdateAndDeleteUser(): void
    {
        // Get the JWT token
        $response = static::createClient()->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $token = $response->toArray()['token'];

        // Create a new user to update
        $response = static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'email' => 'testuser444@example.com',
                'password' => 'securepassword',
                'roles' => ['ROLE_USER'],
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);

        $userId = $response->toArray()['id'];

        // Update the user's information
        $response = static::createClient()->request('PUT', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'email' => 'updateduser@example.com',
                'password' => 'newsecurepassword',
                'roles' => ['ROLE_USER'],
            ],
        ]);

        // Assert the update response is successful
        $this->assertResponseStatusCodeSame(200);

        // Delete the updated user
        $response = static::createClient()->request('DELETE', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}
