<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

abstract class ApiWebTestCase extends WebTestCase
{
    private static bool $databaseReady = false;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        self::bootDatabase();
    }

    protected function registerUser(
        KernelBrowser $client,
        string $email,
        string $password = 'testpass123',
    ): void {
        $client->request(
            'POST',
            '/api/auth/register',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => $email,
                'password' => $password,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(201);
    }

    protected function loginAndGetToken(
        KernelBrowser $client,
        string $email,
        string $password,
    ): string {
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

    protected function authHeaders(string $token): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ];
    }

    protected function fetchFirstStoryId(KernelBrowser $client, string $token): int
    {
        $client->request('GET', '/api/stories', server: $this->authHeaders($token));

        $this->assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $this->assertNotEmpty($payload['items'], 'Expected at least one published story in fixtures.');

        return (int) $payload['items'][0]['id'];
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
