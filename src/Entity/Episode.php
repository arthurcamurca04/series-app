<?php

namespace App\Entity;

use App\Repository\EpisodesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodesRepository::class)]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $number;

    #[ORM\ManyToOne(inversedBy: 'episodes')]
    #[ORM\JoinColumn(nullable: false)]
    private Season $season;

    #[ORM\Column]
    private bool $watched = false;

    /**
     * @param int $number
     */
    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function isWatched(): bool
    {
        return $this->watched;
    }

    public function setWatched(bool $watched): self
    {
        $this->watched = $watched;

        return $this;
    }
}
