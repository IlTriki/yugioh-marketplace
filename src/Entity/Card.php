<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $frameType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $atk = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $def = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $level = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $race = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $attribute = null;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: CardSet::class, cascade: ['persist', 'remove'])]
    private Collection $cardSets;

    #[ORM\OneToMany(mappedBy: 'card', targetEntity: CardImage::class, cascade: ['persist', 'remove'])]
    private Collection $cardImages;

    #[ORM\OneToOne(mappedBy: 'card', targetEntity: Stock::class, cascade: ['persist', 'remove'])]
    private ?Stock $stock;

    public function __construct()
    {
        $this->cardSets = new ArrayCollection();
        $this->cardImages = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getFrameType(): ?string
    {
        return $this->frameType;
    }

    public function setFrameType(?string $frameType)
    {
        $this->frameType = $frameType;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getAtk(): ?int
    {
        return $this->atk;
    }

    public function setAtk(?int $atk)
    {
        $this->atk = $atk;
        return $this;
    }

    public function getDef(): ?int
    {
        return $this->def;
    }

    public function setDef(?int $def)
    {
        $this->def = $def;
        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level)
    {
        $this->level = $level;
        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(?string $race)
    {
        $this->race = $race;
        return $this;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function setAttribute(?string $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getCardSets(): Collection
    {
        return $this->cardSets;
    }

    public function addCardSet(CardSet $cardSet)
    {
        if (!$this->cardSets->contains($cardSet)) {
            $this->cardSets[] = $cardSet;
            $cardSet->setCard($this);
        }

        return $this;
    }

    public function removeCardSet(CardSet $cardSet)
    {
        if ($this->cardSets->contains($cardSet)) {
            $this->cardSets->removeElement($cardSet);
            if ($cardSet->getCard() === $this) {
                $cardSet->setCard(null);
            }
        }

        return $this;
    }

    public function getCardImages(): Collection
    {
        return $this->cardImages;
    }

    public function addCardImage(CardImage $cardImage)
    {
        if (!$this->cardImages->contains($cardImage)) {
            $this->cardImages[] = $cardImage;
            $cardImage->setCard($this);
        }

        return $this;
    }

    public function removeCardImage(CardImage $cardImage)
    {
        if ($this->cardImages->contains($cardImage)) {
            $this->cardImages->removeElement($cardImage);
            if ($cardImage->getCard() === $this) {
                $cardImage->setCard(null);
            }
        }

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock)
    {
        $this->stock = $stock;
        return $this;
    }
}
