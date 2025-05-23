<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Field\Id { __construct as generateId; }
    use Field\Timestampable;
    use TimestampableEntity;

    #[ORM\Column(name: 'username', type: 'string', length: 180, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter a username')]
    private string $username;

    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'Please enter an email')]
    #[Assert\Email()]
    private string $email;

    /** @var array<string> */
    #[ORM\Column(name: 'roles', type: 'json', nullable: false)]
    private array $roles = [];

    #[ORM\Column(name: 'password', type: 'string', nullable: false)]
    private string $password;

    #[ORM\Column(name: 'password_confirmation_token', type: 'string', nullable: true)]
    private ?string $passwordConfirmationToken = null;

    #[ORM\Column(name: 'email_confirmed_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $emailConfirmed = null;

    #[ORM\Column(name: 'timezone', type: 'string', nullable: false, options: ['default' => 'Europe/Paris'])]
    private string $timezone = 'Europe/Paris';

    #[ORM\Column(name: 'locale', type: 'string', nullable: false, options: ['default' => 'fr'])]
    private string $locale = 'fr';

    /** Used in forms, mostly, because, you know, DTOs in EasyAdmin are super annoying to do. */
    public array $formNewRoles = [];
    public ?string $formNewPassword = '';

    public function __construct()
    {
        $this->generateId();
        $this->generateTimestamps();
    }

    public function __toString(): string
    {
        return $this->username.' ('.$this->email.')';
    }

    public function isOwnerOf(HasCreators $subject): bool
    {
        return array_any($subject->getCreators()->toArray(), fn ($creator) => $creator->getId() === $this->id);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = \array_unique($roles + ['ROLE_USER']);
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
        $this->setRoles($this->roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPasswordConfirmationToken(): ?string
    {
        return $this->passwordConfirmationToken;
    }

    public function setConfirmationToken(?string $generateToken)
    {
        $this->passwordConfirmationToken = $generateToken;
    }

    public function eraseCredentials(): void
    {
        $this->formNewPassword = null;
    }

    public function emailConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->emailConfirmed;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed !== null;
    }

    public function setEmailConfirmed(bool $confirmed = true): void
    {
        $this->emailConfirmed = $confirmed ? new \DateTimeImmutable() : null;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}
