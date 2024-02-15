<?php

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\UtilisateurRepository;
use App\Traits\GenericTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[ORM\Table(name: 'utilisateur')]
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Un utilisateur avec cet email existe déjà.')]
#[ORM\Index(columns: ['nom', 'prenom'], flags: ['fulltext'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface, GenericTraitInterface, EquatableInterface
{
    use GenericTrait;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Nom obligatoire')]
    protected ?string $nom;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Prénom obligatoire')]
    protected ?string $prenom;

    #[AppAssert\Civilite]
    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Cilivilité obligatoire')]
    protected ?string $civilite = '';

    #[ORM\Column(name: 'nb_connexion_KO', type: 'integer', nullable: true)]
    protected ?int $nbConnexionKO;

    #[ORM\Column(name: 'email', type: 'string', length: 70, unique: true)]
    #[Assert\NotBlank(message: 'Adresse email obligatoire')]
    #[Assert\Email(message: 'Adresse email non valide')]
    #[Assert\Length(max: 70, maxMessage: "L'adresse email ne doit pas dépasser {{ limit }} caractères")]
    private ?string $email;

    #[ORM\Column(name: 'email_canonical', type: 'string', length: 70, unique: true, nullable: true)]
    protected ?string $emailCanonical;

    #[ORM\Column(name: 'username_canonical', type: 'string', length: 255, unique: true, nullable: true)]
    protected ?string $usernameCanonical;

    #[ORM\Column(name: 'roles', type: 'json')]
    private array $roles;

    #[ORM\Column(name: 'password', type: 'string')]
    private ?string $password;

    #[ORM\Column(name: 'salt', type: 'string', nullable: true)]
    private ?string $salt = null;

    #[ORM\Column(name: 'locked', type: 'boolean')]
    private ?bool $locked;

    #[ORM\Column(name: 'enabled', type: 'boolean')]
    private ?bool $enabled;

    #[ORM\Column(name: 'expired', type: 'boolean')]
    private ?bool $expired;

    #[ORM\Column(name: 'expires_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $expiresAt;

    #[ORM\Column(name: 'confirmation_token', type: 'string', nullable: true)]
    private ?string $confirmationToken;

    #[ORM\Column(name: 'password_requested_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $passwordRequestedAt;

    #[ORM\Column(name: 'recevoir_notifications_goldy', type: 'boolean')]
    private ?bool $recevoirNotifGoldy = true;

    #[ORM\Column(name: 'force_change_password', type: 'boolean', nullable: true)]
    private ?bool $forceChangePassword = false;

    #[Assert\EqualTo(propertyPath: "password" )]
    #[Assert\NotBlank(message: 'Le mot de passe n\'est pas identique.')]
    private $passwordConfirm;


    public function __construct()
    {
        //$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = true;
        $this->locked = false;
        $this->expired = false;
        $this->roles = [];
        $this->nbConnexionKO = 0;
        $this->messages = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function getNbConnexionKO(): ?int
    {
        return $this->nbConnexionKO;
    }

    public function setNom(?string $nom): static
    {
        // Nom en majuscules
        $this->nom = strtoupper($nom);

        return $this;
    }

    public function setPrenom(?string $prenom): static
    {
        // Prénom commence par une majuscule
        $this->prenom = ucwords(mb_strtolower($prenom, 'UTF-8'));

        return $this;
    }

    public function setCivilite(?string $civilite): static
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function setNbConnexionKO(?int $nbConnexionKO): static
    {
        $this->nbConnexionKO = $nbConnexionKO;

        return $this;
    }


    public function setEmail(?string $email): static
    {
        $this->email = mb_strtolower($email, 'UTF-8');
        $this->usernameCanonical = $email;
        $this->emailCanonical = $this->email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRecevoirNotifGoldy(): ?bool
    {
        return $this->recevoirNotifSchool;
    }

    public function setRecevoirNotifGoldy(?bool $recevoirNotifSchool): static
    {
        $this->recevoirNotifSchool = $recevoirNotifSchool;

        return $this;
    }

    public function getMessages(): ?Collection
    {
        return $this->messages;
    }

    #[ORM\PrePersist]
    public function initialiazeRecevoirNotifGoldy(): void
    {
        // Cette fonction est appelée quand on persiste une entité utilisateur
        // elle permet d'initialiser le champ recevoirNotifSchool à false si l'utilisateur a un role DGAFP VIP, sinon à true

        if (in_array('ROLE_PROFESSEUR', $this->getRoles())) {
            //Les utilisateurs avec le role DGAFP VIP ne recevront pas les notifications de signac par mail par défaut
            $this->recevoirNotifSchool = false;
        } else {
            //Les utilisateurs recevront les notifications de signac par mail par défaut
            $this->recevoirNotifSchool = true;
        }
    }

    public function getRoleImpression(): string
    {
        return match ($this->roles[0]) {
            'ROLE_PROFESSEUR' => 'Professeur',
            'ROLE_ELEVE' => 'Eleve',
            'ROLE_MIN_VIP' => 'Manageur',
            default => 'Role non reconnu',
        };
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(?bool $locked): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    public function isExpired(): ?bool
    {
        return !$this->isAccountNonExpired();
    }

    public function setExpired($boolean): static
    {
        $this->expired = (bool) $boolean;

        return $this;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt = null): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt = null): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(?string $role): static
    {
        $role = strtoupper($role);
        if ('ROLE_USER' === $role) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // S'assure que chaque utilisateur possède le rôle ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function isAccountNonExpired(): bool
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isPasswordRequestNonExpired($ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function hasRole(?string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function removeRole(?string $roleUser): static
    {
        $index = array_search($roleUser, $this->roles);
        if (false !== $index) {
            unset($this->roles[$index]);
        }

        return $this;
    }

    public function isForceChangePassword(): ?bool
    {
        return $this->forceChangePassword;
    }

    public function setForceChangePassword(?bool $forceChangePassword): self
    {
        $this->forceChangePassword = $forceChangePassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        $currentRoles = array_map('strval', (array) $this->getRoles());
        $newRoles = array_map('strval', (array) $user->getRoles());
        $rolesChanged = \count($currentRoles) !== \count($newRoles) || \count($currentRoles) !== \count(array_intersect($currentRoles, $newRoles));
        if ($rolesChanged) {
            return false;
        }

        if ($this->getUserIdentifier() !== $user->getUserIdentifier()) {
            return false;
        }

        if ($this->isEnabled() !== $user->isEnabled()) {
            return false;
        }

        if ($this->isLocked() !== $user->isLocked()) {
            return false;
        }

        return true;
    }

    public function getEmailCanonical(): ?string
    {
        return $this->emailCanonical;
    }

    public function setEmailCanonical(?string $emailCanonical): static
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    public function getUsernameCanonical(): ?string
    {
        return $this->usernameCanonical;
    }

    public function setUsernameCanonical(?string $usernameCanonical): static
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    public function setSalt(?string $salt): static
    {
        $this->salt = $salt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function RecevoirNotifGoldy(): ?bool
    {
        return $this->recevoirNotifGoldy;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setDestinataire($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getDestinataire() === $this) {
                $message->setDestinataire(null);
            }
        }

        return $this;
    }


    public function getRole(): ?string
    {
        //Un recupère l'index 0 car un utilisateur n'a qu'un seul rôle fonctionnel
        $roles = $this->getRoles();
        $this->role = $roles[0];

        return $this->role;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function isRecevoirNotifGoldy(): ?bool
    {
        return $this->recevoirNotifGoldy;
    }


}