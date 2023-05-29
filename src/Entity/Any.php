<?php

namespace App\Entity;

use App\Repository\AnyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnyRepository::class)]
class Any
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'anies')]
    private ?user $Users = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUsers(): ?user
    {
        return $this->Users;
    }

    public function setUsers(?user $Users): self
    {
        $this->Users = $Users;

        return $this;
    }
}
