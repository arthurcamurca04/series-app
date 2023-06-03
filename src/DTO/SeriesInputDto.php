<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints as Asserts;
class SeriesInputDto
{
    #[Asserts\NotBlank]
    #[Asserts\Length(min: 3, minMessage: 'Nome da série deve conter no mínimo 3 caracteres')]
    private string $name;

    #[Asserts\Positive]
    private int $seasonsQuantity;

    #[Asserts\Positive]
    private int $episodesQuantity;

    /**
     * @param string $seriesName
     * @param int $seasonsQuantity
     * @param int $episodesQuantity
     */
    public function __construct(
        string $seriesName = '',
        int $seasonsQuantity = 0,
        int $episodesQuantity = 0
    )
    {
        $this->name = $seriesName;
        $this->seasonsQuantity = $seasonsQuantity;
        $this->episodesQuantity = $episodesQuantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSeasonsQuantity(): int
    {
        return $this->seasonsQuantity;
    }

    /**
     * @param int $seasonsQuantity
     */
    public function setSeasonsQuantity(int $seasonsQuantity): void
    {
        $this->seasonsQuantity = $seasonsQuantity;
    }

    /**
     * @return int
     */
    public function getEpisodesQuantity(): int
    {
        return $this->episodesQuantity;
    }

    /**
     * @param int $episodesQuantity
     */
    public function setEpisodesQuantity(int $episodesQuantity): void
    {
        $this->episodesQuantity = $episodesQuantity;
    }
}