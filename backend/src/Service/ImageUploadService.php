<?php

declare(strict_types=1);

namespace App\Service;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageUploadService
{
    private const MAX_BYTES = 5_242_880;

    /** @var list<string> */
    private const ALLOWED_MIME = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array{url: string, filename: string}
     */
    public function store(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Fichier invalide.');
        }

        if ($file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('Image trop volumineuse (max 5 Mo).');
        }

        $mime = (string) $file->getMimeType();
        if (!\in_array($mime, self::ALLOWED_MIME, true)) {
            throw new InvalidArgumentException('Format non supporté (JPEG, PNG, WebP, GIF).');
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'bin',
        };

        $uploadDir = $this->projectDir.'/public/images/uploads';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new RuntimeException('Impossible de créer le dossier uploads.');
        }

        $filename = bin2hex(random_bytes(16)).'.'.$extension;
        $file->move($uploadDir, $filename);

        return [
            'url' => '/images/uploads/'.$filename,
            'filename' => $filename,
        ];
    }
}
