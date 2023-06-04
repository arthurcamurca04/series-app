<?php

namespace App\DTO;

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

    #[Asserts\File(mimeTypes: 'image/*')]
    private ?string $coverImage;

    /**
     * @param string $seriesName
     * @param int $seasonsQuantity
     * @param int $episodesQuantity
     * @param string|null $coverImage
     */
    public function __construct(
        string $seriesName = '',
        int $seasonsQuantity = 0,
        int $episodesQuantity = 0,
        string $coverImage = null
    )
    {
        $this->name = $seriesName;
        $this->seasonsQuantity = $seasonsQuantity;
        $this->episodesQuantity = $episodesQuantity;
        $this->coverImage = $coverImage;
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

    /**
     * @return string|null
     */
    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    /**
     * @param string $coverImage
     */
    public function setCoverImage(string $coverImage): void
    {
        $this->coverImage = $coverImage;
    }
}