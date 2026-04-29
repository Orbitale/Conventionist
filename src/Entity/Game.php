<?php

namespace App\Entity;

use App\Enum\GameComplexity;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: 'games')]
class Game implements HasCreators
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Creators { Field\Creators::__construct as generateCreators; }
    use Field\Description;
    use Field\Slug;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a title')]
    private string $title = '';

    #[ORM\Column(name: 'publisher', type: Types::STRING, length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(name: 'min_players', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $minPlayers = null;

    #[ORM\Column(name: 'max_players', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $maxPlayers = null;

    #[ORM\Column(name: 'duration_minutes', type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $durationMinutes = null;

    #[ORM\Column(name: 'complexity', type: Types::STRING, length: 32, enumType: GameComplexity::class, nullable: true)]
    private ?GameComplexity $complexity = null;

    /** @var Collection<int, GameCategory> */
    #[ORM\ManyToMany(targetEntity: GameCategory::class)]
    #[ORM\JoinTable(name: 'games_categories')]
    private Collection $categories;

    /** @var Collection<int, GameTheme> */
    #[ORM\ManyToMany(targetEntity: GameTheme::class)]
    #[ORM\JoinTable(name: 'games_themes')]
    private Collection $themes;

    public function __construct()
    {
        $this->generateId();
        $this->generateCreators();
        $this->generateTimestamps();
        $this->categories = new ArrayCollection();
        $this->themes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title ?: '';
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title ?: '';
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): void
    {
        $this->publisher = $publisher;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(?int $minPlayers): void
    {
        $this->minPlayers = $minPlayers;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(?int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): void
    {
        $this->durationMinutes = $durationMinutes;
    }

    public function getComplexity(): ?GameComplexity
    {
        return $this->complexity;
    }

    public function setComplexity(?GameComplexity $complexity): void
    {
        $this->complexity = $complexity;
    }

    /**
     * @return Collection<int, GameCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(GameCategory $category): void
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
    }

    public function removeCategory(GameCategory $category): void
    {
        $this->categories->removeElement($category);
    }

    /**
     * @return Collection<int, GameTheme>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(GameTheme $theme): void
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
        }
    }

    public function removeTheme(GameTheme $theme): void
    {
        $this->themes->removeElement($theme);
    }
}
