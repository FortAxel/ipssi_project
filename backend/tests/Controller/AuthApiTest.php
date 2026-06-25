<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class AuthApiTest extends ApiWebTestCase
{
    public function testRegisterAndLogin(): void
    {
        $client = static::createClient();
        $email = 'auth-smoke@demo.local';
        $password = 'testpass123';

        $this->registerUser($client, $email, $password);

        $token = $this->loginAndGetToken($client, $email, $password);

        $client->request('GET', '/api/me', server: $this->authHeaders($token));

        $this->assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $this->assertSame($email, $payload['email']);
        $this->assertSame('Test', $payload['firstName']);
    }
}
