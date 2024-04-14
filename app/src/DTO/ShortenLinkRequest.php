<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ShortenLinkRequest
{
    #[Assert\NotBlank(message: "URL is required")]
    #[Assert\Url(message: "Invalid URL format")]
    public ?string $url;

    // Keep as string for validation
    #[Assert\NotBlank(message: "Expiration date is required")]
    #[Assert\Callback(callback: ['App\DTO\ShortenLinkRequest', 'validateExpirationDate'])]
    public ?string $expiration;

    public function __construct(array $data)
    {
        $this->url = $data['url'] ?? null;
        $this->expiration = $data['expiration'] ?? null; // Keep as string for validation
    }

    public static function validateExpirationDate($object, ExecutionContextInterface $context, $payload)
    {
        if (isset($object)) {
            $expirationDateTime = \DateTime::createFromFormat('Y-m-d\TH:i', $object);
            if (false === $expirationDateTime) {
                $context->buildViolation('Invalid datetime format')
                    ->addViolation();
            } elseif ($expirationDateTime < new \DateTime()) {
                $context->buildViolation('Expiration date must be today or in the future')
                    ->addViolation();
            }
        }
    }
}

