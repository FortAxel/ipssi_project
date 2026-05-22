<?php

declare(strict_types=1);

namespace App\Service\Tts;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Synthèse via Microsoft Edge TTS (gratuit, sans clé API) — CLI edge-tts (Python).
 */
final class EdgeTtsSynthesizer
{
    public function __construct(
        private readonly string $voice,
        private readonly string $rate,
        private readonly string $volume,
        private readonly string $pitch,
        private readonly int $maxChars,
        private readonly string $binary = 'edge-tts',
    ) {
    }

    public function getVoice(): string
    {
        return $this->voice;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function synthesize(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
        if ($text === '') {
            throw new InvalidArgumentException('Texte vide.');
        }
        if (mb_strlen($text) > $this->maxChars) {
            $text = mb_substr($text, 0, $this->maxChars);
        }

        $outputFile = tempnam(sys_get_temp_dir(), 'edge_tts_');
        if ($outputFile === false) {
            throw new RuntimeException('Impossible de créer un fichier temporaire.');
        }
        $mediaPath = $outputFile.'.mp3';

        try {
            $process = new Process([
                $this->binary,
                '--voice', $this->voice,
                '--rate='.$this->rate,
                '--volume='.$this->volume,
                '--pitch='.$this->pitch,
                '--text', $text,
                '--write-media', $mediaPath,
            ]);
            $process->setTimeout(90);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            if (!is_readable($mediaPath)) {
                throw new RuntimeException('Fichier audio non généré.');
            }

            $audio = file_get_contents($mediaPath);
            if ($audio === false || $audio === '') {
                throw new RuntimeException('Fichier audio vide.');
            }

            return $audio;
        } finally {
            @unlink($outputFile);
            @unlink($mediaPath);
        }
    }
}
