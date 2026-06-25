<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class FavoriteApiTest extends ApiWebTestCase
{
    public function testToggleFavoriteAndUserIsolation(): void
    {
        $client = static::createClient();

        $this->registerUser($client, 'favorite-a@demo.local');
        $tokenA = $this->loginAndGetToken($client, 'favorite-a@demo.local', 'testpass123');
        $storyId = $this->fetchFirstStoryId($client, $tokenA);

        $client->request(
            'POST',
            '/api/favorites/toggle',
            server: $this->authHeaders($tokenA),
            content: json_encode(['storyId' => $storyId], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseIsSuccessful();
        $toggle = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertTrue($toggle['isFavorite']);

        $client->request('GET', '/api/favorites', server: $this->authHeaders($tokenA));
        $this->assertResponseIsSuccessful();
        $favoritesA = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertCount(1, $favoritesA['items']);
        $this->assertSame($storyId, $favoritesA['items'][0]['id']);

        $this->registerUser($client, 'favorite-b@demo.local');
        $tokenB = $this->loginAndGetToken($client, 'favorite-b@demo.local', 'testpass123');

        $client->request('GET', '/api/favorites', server: $this->authHeaders($tokenB));
        $this->assertResponseIsSuccessful();
        $favoritesB = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertCount(0, $favoritesB['items']);

        $client->request(
            'POST',
            '/api/favorites/toggle',
            server: $this->authHeaders($tokenA),
            content: json_encode(['storyId' => $storyId], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseIsSuccessful();
        $untoggle = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        $this->assertFalse($untoggle['isFavorite']);
    }
}
