<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

class UserRepository extends AbstractRepository
{
    public function save(User $user): void
    {
        $connection = self::getConnection();

        if ($user->getId() !== null) {
            $sql = "UPDATE users 
                    SET name = :name, email = :email, password = :password, enabled = :enabled, updated_at = :updated_at
                    WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':id', $user->getId(), PDO::PARAM_INT);
        } else {
            $sql = "INSERT INTO users (name, email, password, enabled, created_at, updated_at)
                    VALUES (:name, :email, :password, :enabled, :created_at, :updated_at)";
            $stmt = $connection->prepare($sql);
        }

        $stmt->bindValue(':name', $user->getName());
        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':enabled', $user->isEnabled() ? 1 : 0);
        $stmt->bindValue(':created_at', $user->getCreatedAt());
        $stmt->bindValue(':updated_at', $user->getUpdatedAt());

        $stmt->execute();

        if ($user->getId() === null) {
            $user->setId((int)$connection->lastInsertId());
        }
    }

    public function findByEmail(string $email): ?User
    {
        $connection = self::getConnection();

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $contact = $stmt->fetchObject(User::class);

        return $contact ?: null;
    }

    public function findById(int $id): ?User
    {
        $connection = self::getConnection();

        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $contact = $stmt->fetchObject(User::class);

        return $contact ?: null;
    }
}