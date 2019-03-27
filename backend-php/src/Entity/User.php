<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $googleAccessToken;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googleRefreshToken;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $googleTokenExpiresIn;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $googleTokenScope;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $googleTokenId;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $googleTokenCreated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles, true);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = json_encode($roles);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    /**
     * @param mixed $googleAccessToken
     */
    public function setGoogleAccessToken($googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleTokenExpiresIn()
    {
        return $this->googleTokenExpiresIn;
    }

    /**
     * @param mixed $googleTokenExpiresIn
     */
    public function setGoogleTokenExpiresIn($googleTokenExpiresIn): self
    {
        $this->googleTokenExpiresIn = $googleTokenExpiresIn;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleTokenScope()
    {
        return $this->googleTokenScope;
    }

    /**
     * @param mixed $googleTokenScope
     */
    public function setGoogleTokenScope($googleTokenScope): self
    {
        $this->googleTokenScope = $googleTokenScope;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleTokenType()
    {
        return $this->googleTokenType;
    }

    /**
     * @param mixed $googleTokenType
     */
    public function setGoogleTokenType($googleTokenType): self
    {
        $this->googleTokenType = $googleTokenType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleTokenId()
    {
        return $this->googleTokenId;
    }

    /**
     * @param mixed $googleTokenId
     */
    public function setGoogleTokenId($googleTokenId): self
    {
        $this->googleTokenId = $googleTokenId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleTokenCreated()
    {
        return $this->googleTokenCreated;
    }

    /**
     * @param mixed $googleTokenCreated
     */
    public function setGoogleTokenCreated($googleTokenCreated): self
    {
        $this->googleTokenCreated = $googleTokenCreated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleRefreshToken()
    {
        return $this->googleRefreshToken;
    }

    /**
     * @param mixed $googleRefreshToken
     */
    public function setGoogleRefreshToken($googleRefreshToken): self
    {
        $this->googleRefreshToken = $googleRefreshToken;

        return $this;
    }
}
