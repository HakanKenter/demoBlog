<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields= {"email"},
 * message="Un compte est déjà existant à cette adresse Email !!"
 * )
 */
class User implements UserInterface // On a mis implements UserInterface : quand on fait une implementation on doit redeclarer ls methode de la classe implementer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 8, minMessage = "Votre mot de passe doit faire au minimum 8 caractères")
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passes ne correspondent pas")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passes ne correspondent pas")
     */
    public $confirm_password;

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

    /*
        Pour pouvoir encoder le mot de passe, il faut que notre classe (entité) User implémente de la classe UserInterface
        Il faut absolument déclarer les méthodes getRoles(), getSalt(), eraseCredentials(), getUsername(), getPassword()
    */

    // Cette méthode est uniquement destiné à nettoyer les mots de passe en texte brut éventuellement stockés
    public function eraseCredentials()
    {
        
    }

    // renvoie la chaine de caractère non encodé que l'utilisateur a saisi, qui a été utiliser à l'origin pour encoder le mdp
    public function getSalt()
    {

    }

    // cette méthode renvoi un tableau de chaine de caractere ou sont stockés les rôles accordés à l'utilisateur
    public function getRoles()
    {
        return ['ROLES_USER'];
    }
}
