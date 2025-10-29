<?php

namespace App\Entity;

class Contact
{
    private ?int $id = null;

    private string $name;

    private string $phone;

    private string $email;

    private string $created_at;

    private string $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }


    public function setCreatedAt(): self
    {
        $now = date('Y-m-d H:i:s');
        $this->created_at = $now;
        $this->updated_at = $now;
        return $this;
    }

    public function setUpdatedAt(): self
    {
        $this->updated_at = date('Y-m-d H:i:s');
        return $this;
    }
}