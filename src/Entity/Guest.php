<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GuestRepository::class)
 */
class Guest implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pluses;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="guests")
     */
    private $event;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checkInTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $checkedInPluses;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPluses(): ?int
    {
        return $this->pluses;
    }

    public function setPluses(?int $pluses): self
    {
        $this->pluses = $pluses;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getCheckInTime(): ?\DateTimeInterface
    {
        return $this->checkInTime;
    }

    public function setCheckInTime(?\DateTimeInterface $checkInTime): self
    {
        $this->checkInTime = $checkInTime;

        return $this;
    }

    public function getCheckedInPluses(): ?int
    {
        return $this->checkedInPluses;
    }

    public function setCheckedInPluses(?int $checkedInPluses): self
    {
        $this->checkedInPluses = $checkedInPluses;

        return $this;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
