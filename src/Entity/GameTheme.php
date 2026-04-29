<?php

namespace App\Entity;

use App\Repository\GameThemeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GameThemeRepository::class)]
#[ORM\Table(name: 'game_themes')]
class GameTheme
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Name;
    use Field\Slug;
    use Field\Description;
    use Field\Timestampable;
    use TimestampableEntity;

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }
}
