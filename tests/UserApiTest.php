<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UserApiTest extends ApiTestCase
{
    private function authenticate(): string
    {
        // Create the client to make requests
        $client = static::createClient();

        // Make a POST request to /api/auth to get the JWT token
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

    public function testGetUsers(): void
    {
        $client = static::createClient();
        $token = $this->authenticate();

        $response = $client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($response->getContent());

        $data = json_decode($response->getContent(), true);
        foreach ($data as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('email', $user);
        }
    }

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

        // Vérifier que l'utilisateur a été créé avec succès
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($response->getContent(), true);

        // Vérifier que la réponse contient les données de l'utilisateur créé
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($uniqueEmail, $data['email']);
    }


    public function testGetUserById(): void
    {
        $client = static::createClient();

        $userId = 88;
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

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

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertEquals($userId, $data['id']);
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();

        // Obtenir un token JWT
        $response = $client->request('POST', '/api/auth', [
            'json' => [
                'email' => 'admin@local.host',
                'password' => 'admin_password',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $token = $data['token'];

        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'user_to_delete' . time() . '@local.host',
                'password' => 'password123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $userData = json_decode($response->getContent(), true);
        $userId = $userData['id'];

        $response = $client->request('DELETE', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);

        $response = $client->request('GET', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateUser(): void
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

        $response = $client->request('POST', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'user_to_update' . time() . '@local.host',
                'password' => 'password123',
            ],
        ]);

        $this->assertResponseStatusCodeSame(201);
        $userData = json_decode($response->getContent(), true);
        $userId = $userData['id']; // Récupérer l'ID de l'utilisateur créé

        $response = $client->request('PUT', '/api/users/' . $userId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'email' => 'updated_user' . time() . '@local.host', // Nouvel email
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($userId, $data['id']);
        $this->assertEquals('updated_user' . time() . '@local.host', $data['email']);
    }


}
