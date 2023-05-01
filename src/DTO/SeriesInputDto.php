<?php

namespace App;

class SeriesInputDto
{
    private string $name;
    private int $seasonsQuantity;
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