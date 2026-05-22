<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Tts\EdgeTtsSynthesizer;
use RuntimeException;

final class TtsService
{
    public function __construct(
        private readonly bool $enabled,
        private readonly string $provider,
        private readonly ?EdgeTtsSynthesizer $edgeSynthesizer,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    /** @return array{enabled: bool, provider: string, voice: string|null, rate: string|null} */
    public function getPublicConfig(): array
    {
        return [
            'enabled' => $this->enabled,
            'provider' => $this->provider,
            'voice' => $this->edgeSynthesizer?->getVoice() ?? null,
            'rate' => $this->edgeSynthesizer?->getRate() ?? null,
        ];
    }

    /**
     * @return array{audioBase64: string, mimeType: string, provider: string}
     */
    public function synthesize(string $text): array
    {
        if (!$this->enabled) {
            throw new RuntimeException('TTS désactivé (TTS_ENABLED=false).');
        }

        if ($this->provider !== 'edge' || $this->edgeSynthesizer === null) {
            throw new RuntimeException(\sprintf('Provider TTS non supporté : %s', $this->provider));
        }

        $audio = $this->edgeSynthesizer->synthesize($text);

        return [
            'audioBase64' => base64_encode($audio),
            'mimeType' => 'audio/mpeg',
            'provider' => 'edge',
        ];
    }
}
