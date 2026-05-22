<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\TtsSynthesizeInput;
use App\Service\TtsService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api/tts')]
final class TtsController extends AbstractController
{
    public function __construct(
        private readonly TtsService $ttsService,
    ) {
    }

    #[Route('/config', name: 'api_tts_config', methods: ['GET'])]
    public function config(): JsonResponse
    {
        return $this->json($this->ttsService->getPublicConfig());
    }

    #[Route('/synthesize', name: 'api_tts_synthesize', methods: ['POST'])]
    public function synthesize(#[MapRequestPayload] TtsSynthesizeInput $input): JsonResponse
    {
        if (!$this->ttsService->isEnabled()) {
            return $this->json(
                ['error' => 'tts_disabled', 'message' => 'La synthèse vocale API est désactivée.'],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        }

        try {
            return $this->json($this->ttsService->synthesize($input->text));
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => 'invalid_text', 'message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return $this->json(
                [
                    'error' => 'tts_failed',
                    'message' => 'Synthèse vocale indisponible. Vérifiez que edge-tts est installé (Docker rebuild).',
                ],
                Response::HTTP_SERVICE_UNAVAILABLE,
            );
        }
    }
}
