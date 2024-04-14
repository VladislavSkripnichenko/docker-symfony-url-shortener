<?php

namespace App\Service;

use App\Entity\Link;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;

class LinkService
{
    private $entityManager;
    private $linkRepository;

    public function __construct(EntityManagerInterface $entityManager, LinkRepository $linkRepository)
    {
        $this->entityManager = $entityManager;
        $this->linkRepository = $linkRepository;
    }

    public function createLink(string $originalUrl, ?\DateTime $expirationDate): Link
    {
        $link = new Link();
        $link->setOriginalUrl($originalUrl);
        $link->setShortUrl($this->generateUniqueShortUrl());
        $link->setCreationDate(new \DateTime());
        $link->setExpirationDate($expirationDate);
        $link->setClickCount(0);

        $this->linkRepository->save($link);

        return $link;
    }

    private function generateUniqueShortUrl(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $shortUrl = '';

        do {
            for ($i = 0; $i < 7; $i++) {
                $shortUrl .= $characters[rand(0, $charactersLength - 1)];
            }
        } while ($this->linkRepository->findLinkByShortUrl($shortUrl));

        return $shortUrl;
    }

    public function incrementClickCount(Link $link)
    {
        $link->setClickCount($link->getClickCount() + 1);
        $this->entityManager->flush();
    }
}
