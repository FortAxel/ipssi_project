<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

final class AccountApiTest extends WebTestCase
{
    private static bool $databaseReady = false;

    private const TEST_EMAIL = 'account-test@demo.local';

    private const TEST_PASSWORD = 'testpass123';

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::bootDatabase();
    }

    public function testProfileUpdateAndAccountDeletion(): void
    {
        $client = static::createClient();
        $this->registerUser($client);

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

    private function registerUser(KernelBrowser $client): void
    {
        $client->request(
            'POST',
            '/api/auth/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'firstName' => 'Test',
                'lastName' => 'Account',
                'email' => self::TEST_EMAIL,
                'password' => self::TEST_PASSWORD,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(201);
    }

    private function loginAndGetToken(KernelBrowser $client, string $email, string $password): string
    {
        $client->request(
            'POST',
            '/api/auth/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => $email,
                'password' => $password,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        return $payload['token'];
    }

    private function authHeaders(string $token): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ];
    }

    private static function bootDatabase(): void
    {
        if (self::$databaseReady) {
            return;
        }

        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $commands = [
            ['command' => 'doctrine:database:drop', '--force' => true, '--if-exists' => true],
            ['command' => 'doctrine:database:create', '--if-not-exists' => true],
            ['command' => 'doctrine:schema:drop', '--force' => true, '--full-database' => true],
            ['command' => 'doctrine:schema:create'],
            ['command' => 'doctrine:fixtures:load', '--no-interaction' => true],
        ];

        foreach ($commands as $input) {
            $application->run(new ArrayInput($input), new NullOutput());
        }

        self::$databaseReady = true;
        self::ensureKernelShutdown();
    }
}
