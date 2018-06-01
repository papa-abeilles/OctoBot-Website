<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
    * The length=64 works well with bcrypt algorithm.
    * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }


    /* lignes ajoutées :*/
    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->isActive = true;
    }

    /**
    * @var string
    *
    * This field will not be persisted
    * It is used to store the password in the form
     * @Assert\NotBlank(message="Password cannot be empty", groups={"Update"})
     * @Assert\Regex(
     *      pattern="/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/",
     *      message="Password Error: Use 1 upper case letter, 1 lower case letter, and 1 number",
     *      groups={"Update"}
     * )
     * @Assert\Length(max=4096)
    */
    private $plainPassword;

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password): self
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function getSalt()
    {
        // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
        return null;
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRoles()
    {
        // for the user entity has always at least the role 'ROLE_USER'
        return array_unique(array_merge(['ROLE_USER'], $this->roles));
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->username,
                $this->password,
                // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
                // $this->salt,
            ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
                $this->id,
                $this->username,
                $this->password,
                // we don't need a salt because bcrypt do this internally (algorithm: bcrypt in security.yaml).
                // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}