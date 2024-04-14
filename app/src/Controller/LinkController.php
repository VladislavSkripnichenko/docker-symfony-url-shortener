<?php

namespace App\Controller;

use App\DTO\ShortenLinkRequest;
use App\Entity\Link;
use App\Service\LinkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkController extends AbstractController
{
    private $linkService;
    private $entityManager;

    public function __construct(LinkService $linkService, EntityManagerInterface $entityManager)
    {
        $this->linkService = $linkService;
        $this->entityManager = $entityManager;
    }

    #[Route('/shorten', name: 'shorten_link', methods: ['POST'])]
    public function shortenLink(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $shortenRequest = new ShortenLinkRequest($data);

        $violations = $validator->validate($shortenRequest);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $expirationDate = $shortenRequest->expiration ? new \DateTime($shortenRequest->expiration) : null;

        $link = $this->linkService->createLink($shortenRequest->url, $expirationDate);

        // Create a full URL to the shortened link
        $fullShortUrl =  $this->generateUrl('redirect_short_link', ['shortUrl' => $link->getShortUrl()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->json([
            'shortUrl' => $fullShortUrl,
            'originalUrl' => $link->getOriginalUrl()
        ]);
    }

    #[Route('/{shortUrl}', name: 'redirect_short_link', methods: ['GET'])]
    public function redirectLink(string $shortUrl): Response
    {
        $link = $this->entityManager->getRepository(Link::class)->findLinkByShortUrl($shortUrl);

        if (!$link) {
            throw $this->createNotFoundException('Link not found');
        }

        // Increment the click count
        $this->linkService->incrementClickCount($link);

        // Redirect to the original URL
        return $this->redirect($link->getOriginalUrl());
    }
}
