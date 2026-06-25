<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class ReadingProgressApiTest extends ApiWebTestCase
{
    public function testSaveAndReloadReadingProgress(): void
    {
        $client = static::createClient();

        $this->registerUser($client, 'progress@demo.local');
        $token = $this->loginAndGetToken($client, 'progress@demo.local', 'testpass123');
        $storyId = $this->fetchFirstStoryId($client, $token);

        $client->request('GET', "/api/reading-progress/{$storyId}", server: $this->authHeaders($token));

        $this->assertResponseIsSuccessful();
        $initial = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertSame(1, $initial['lastPageNumber']);
        $this->assertFalse($initial['isCompleted']);

        $client->request(
            'PUT',
            "/api/reading-progress/{$storyId}",
            server: $this->authHeaders($token),
            content: json_encode(['lastPageNumber' => 3], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseIsSuccessful();
        $saved = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertSame(3, $saved['lastPageNumber']);
        $this->assertFalse($saved['isCompleted']);

        $client->request('GET', "/api/reading-progress/{$storyId}", server: $this->authHeaders($token));

        $this->assertResponseIsSuccessful();
        $reloaded = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertSame(3, $reloaded['lastPageNumber']);
        $this->assertNotNull($reloaded['startedAt']);
        $this->assertNotNull($reloaded['lastReadAt']);
    }
}
