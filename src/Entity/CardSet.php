<?php

namespace App\Entity;

use App\Repository\CardSetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardSetRepository::class)]
class CardSet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Card::class, inversedBy: 'cardSets')]
    #[ORM\JoinColumn(nullable: false)]
    private Card $card;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $setName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $setCode = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $setRarity = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $setRarityCode = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2, nullable: true)]
    private ?float $setPrice = null;

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

    public function getSetName(): ?string
    {
        return $this->setName;
    }

    public function setSetName(?string $setName)
    {
        $this->setName = $setName;
        return $this;
    }

    public function getSetCode(): ?string
    {
        return $this->setCode;
    }

    public function setSetCode(?string $setCode)
    {
        $this->setCode = $setCode;
        return $this;
    }

    public function getSetRarity(): ?string
    {
        return $this->setRarity;
    }

    public function setSetRarity(?string $setRarity)
    {
        $this->setRarity = $setRarity;
        return $this;
    }

    public function getSetRarityCode(): ?string
    {
        return $this->setRarityCode;
    }

    public function setSetRarityCode(?string $setRarityCode)
    {
        $this->setRarityCode = $setRarityCode;
        return $this;
    }

    public function getSetPrice(): ?float
    {
        return $this->setPrice;
    }

    public function setSetPrice(?float $setPrice)
    {
        $this->setPrice = $setPrice;
        return $this;
    }
}