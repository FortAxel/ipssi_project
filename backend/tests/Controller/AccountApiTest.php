<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class AccountApiTest extends ApiWebTestCase
{
    private const TEST_EMAIL = 'account-test@demo.local';

    private const TEST_PASSWORD = 'testpass123';

    public function testProfileUpdateAndAccountDeletion(): void
    {
        $client = static::createClient();
        $this->registerUser($client, self::TEST_EMAIL, self::TEST_PASSWORD);

        $token = $this->loginAndGetToken($client, self::TEST_EMAIL, self::TEST_PASSWORD);

        $client->request(
            'PATCH',
            '/api/me',
            server: $this->authHeaders($token),
            content: json_encode([
                'currentPassword' => self::TEST_PASSWORD,
                'newPassword' => 'newpass456',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseIsSuccessful();

        $token = $this->loginAndGetToken($client, self::TEST_EMAIL, 'newpass456');

        $client->request(
            'DELETE',
            '/api/me',
            server: $this->authHeaders($token),
            content: json_encode([
                'currentPassword' => 'newpass456',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(204);
    }
}
