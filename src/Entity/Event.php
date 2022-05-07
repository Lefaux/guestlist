<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Guest::class, mappedBy="event", cascade={"persist"})
     * @ORM\OrderBy({"firstName"="ASC", "lastName"="ASC"})
     */
    private $guests;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $eventStart;

    public function __construct()
    {
        $this->guests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Guest>
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function addGuest(Guest $guest): self
    {
        if (!$this->guests->contains($guest)) {
            $this->guests[] = $guest;
            $guest->setEvent($this);
        }

        return $this;
    }

    public function removeGuest(Guest $guest): self
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getEvent() === $this) {
                $guest->setEvent(null);
            }
        }

        return $this;
    }

    public function getTotalGuests(): int
    {
        $totalGuests = 0;
        /** @var Guest $guest */
        foreach ($this->guests as $guest) {
            $totalGuests += ((int)$guest->getPluses() + 1);
        }
        return $totalGuests;
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    public function getEventStart(): ?\DateTimeInterface
    {
        return $this->eventStart;
    }

    public function setEventStart(\DateTimeInterface $eventStart): self
    {
        $this->eventStart = $eventStart;

        return $this;
    }
}
