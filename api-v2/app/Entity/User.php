<?php

namespace App\Entity;

class User
{
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $password;
    private bool $enabled;
    private string $created_at;
    private string $updated_at;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }


    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    public function setEnabled($enabled): self
    {
        $this->enabled = (bool)$enabled;
        return $this;
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
