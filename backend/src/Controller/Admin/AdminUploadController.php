<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ImageUploadService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api/admin')]
final class AdminUploadController extends AbstractController
{
    public function __construct(
        private readonly ImageUploadService $imageUploadService,
    ) {
    }

    #[Route('/upload', name: 'api_admin_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');
        if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            return $this->json([
                'error' => 'missing_file',
                'message' => 'Aucun fichier envoyé.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->imageUploadService->store($file);
        } catch (InvalidArgumentException $e) {
            return $this->json([
                'error' => 'invalid_file',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            $detail = $e->getMessage();

            return $this->json([
                'error' => 'upload_failed',
                'message' => 'Échec de l’envoi de l’image.',
                'detail' => $detail !== '' ? $detail : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($result, Response::HTTP_CREATED);
    }
}
