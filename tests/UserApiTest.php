<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserApiTest extends ApiTestCase
{
    /**
     * Authenticate a user and return the JWT token
     */
    private function authenticate(): string
    {
        $client = static::createClient();

        // Make a POST request to get the JWT token
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        // Assert that authentication was successful and retrieve the token
        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent(), true);

        return $data['token'];
    }

    /**
     * Test the GET /api/users endpoint
     */
    public function testGetUsers(): void
    {
        $client = static::createClient();
        $token = $this->authenticate();

        // Make a GET request to get the list of users
        $response = $client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($response->getContent());

        // Assert that the response contains the expected data
        $data = json_decode($response->getContent(), true);
        foreach ($data as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('email', $user);
        }
    }

    /**
     * Test the POST /api/users endpoint
     */
    public function testCreateUser(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        $uniqueEmail = 'newuser' . uniqid() . '@local.host';

        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => $uniqueEmail,
                'password' => 'newpassword123',
            ],
        ]);

        // Verificate that the response is successful
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);

        // Verificate that the response contains the expected data
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($uniqueEmail, $data['email']);
    }


    /**
     * Test the GET /api/users/{id} endpoint
     */
    public function testGetUserById(): void
    {
        $client = static::createClient();

        $userId = 88; //id of the user to get
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);


        // Get the token
        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        $response = $client->request('GET', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertNotEmpty($response->getContent());

        $data = json_decode($response->getContent(), true);

        // Assert that the response contains the expected data
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertEquals($userId, $data['id']);
    }

    /**
     * Test the DELETE /api/users/{id} endpoint
     */
    public function testDeleteUser(): void
    {
        $client = static::createClient();

        // Authenticate the user
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        // Create a new user
        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'user_to_delete' . time() . '@local.host',
                'password' => 'password123',
            ],
        ]);

        // Verificate that the response is successful
        $this->assertResponseStatusCodeSame(201);
        $userData = json_decode($response->getContent(), true);
        $userId = $userData['id'];

        // Delete the user
        $response = $client->request('DELETE', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Check that the user has been deleted
        $response = $client->request('GET', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test the PUT /api/users/{id} endpoint
     */
    public function testUpdateUser(): void
    {
        $client = static::createClient();

        // Authenticate the user
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        // Create a new user
        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'user_to_update' . time() . '@local.host',
                'password' => 'password123',
            ],
        ]);

        // Verificate that the response is successful
        $this->assertResponseStatusCodeSame(201);
        $userData = json_decode($response->getContent(), true);
        $userId = $userData['id']; // Récupérer l'ID de l'utilisateur créé

        // Update the user
        $response = $client->request('PUT', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'updated_user' . time() . '@local.host', // Nouvel email
            ],
        ]);

        $this->assertResponseIsSuccessful();

        // Check that the user has been updated
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($userId, $data['id']);
        $this->assertEquals('updated_user' . time() . '@local.host', $data['email']);
    }


}
