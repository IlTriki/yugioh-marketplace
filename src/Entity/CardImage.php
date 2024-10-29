<?php

namespace App\Entity;

use App\Repository\CardImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardImageRepository::class)]
class CardImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Card::class, inversedBy: 'cardImages')]
    #[ORM\JoinColumn(nullable: false)]
    private Card $card;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageUrlSmall = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageUrlCropped = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(?Card $card)
    {
        $this->card = $card;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getImageUrlSmall(): ?string
    {
        return $this->imageUrlSmall;
    }

    public function setImageUrlSmall(?string $imageUrlSmall)
    {
        $this->imageUrlSmall = $imageUrlSmall;
        return $this;
    }

    public function getImageUrlCropped(): ?string
    {
        return $this->imageUrlCropped;
    }

    public function setImageUrlCropped(?string $imageUrlCropped)
    {
        $this->imageUrlCropped = $imageUrlCropped;
        return $this;
    }
}