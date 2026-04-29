<?php

namespace App\Entity;

use App\Enum\SafetyToolType;
use App\Repository\SafetyToolRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: SafetyToolRepository::class)]
#[ORM\Table(name: 'safety_tools')]
class SafetyTool
{
    use Field\Id { Field\Id::__construct as private generateId; }
    use Field\Name;
    use Field\Slug;
    use Field\Description;
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'type', type: Types::STRING, length: 32, enumType: SafetyToolType::class, nullable: false)]
    private SafetyToolType $type = SafetyToolType::OTHER;

    #[ORM\Column(name: 'content_warnings', type: Types::TEXT, nullable: false, options: ['default' => ''])]
    private string $contentWarnings = '';

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }

    public function getType(): SafetyToolType
    {
        return $this->type;
    }

    public function setType(SafetyToolType $type): void
    {
        $this->type = $type;
    }

    public function getContentWarnings(): string
    {
        return $this->contentWarnings;
    }

    public function setContentWarnings(?string $contentWarnings): void
    {
        $this->contentWarnings = $contentWarnings ?: '';
    }
}
