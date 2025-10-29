<?php

namespace App\Repository;

use App\Entity\Contact;
use PDO;

class ContactRepository extends AbstractRepository
{
    /**
     * Guarda un contacto en la base de datos.
     */
    public function save(Contact $contact): void
    {
        $connection = self::getConnection();

        if ($contact->getId() !== null) {
            $sql = "UPDATE contacts 
                    SET name = :name, phone = :phone, email = :email, updated_at = NOW() 
                    WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':id', $contact->getId(), PDO::PARAM_INT);
        } else {
            $sql = "INSERT INTO contacts (name, phone, email, created_at, updated_at)
                    VALUES (:name, :phone, :email, NOW(), NOW())";
            $stmt = $connection->prepare($sql);
        }

        $stmt->bindValue(':name', $contact->getName());
        $stmt->bindValue(':phone', $contact->getPhone());
        $stmt->bindValue(':email', $contact->getEmail());

        $stmt->execute();

        if ($contact->getId() === null) {
            $contact->setId((int)$connection->lastInsertId());
        }
    }

    /**
     * Elimina un contacto de la base de datos.
     */
    public function remove(Contact $contact): void
    {
        if ($contact->getId() === null) {
            return;
        }

        $connection = self::getConnection();
        $sql = "DELETE FROM contacts WHERE id = :id";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id', $contact->getId(), PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Lista todos los contactos ordenados por nombre
     */
    public function list(): array
    {
        $connection = self::getConnection();
        $sql = "SELECT * FROM contacts ORDER BY name ASC";
        $stmt = $connection->query($sql);

        return $stmt->fetchAll(PDO::FETCH_CLASS, Contact::class);
    }

    /**
     * Encuentra contactos por nombre (contiene)
     */
    public function find(int $id): ?Contact
    {
        $connection = self::getConnection();
        $sql = "SELECT * FROM contacts WHERE id = :id LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $contact = $stmt->fetchObject(Contact::class);

        return $contact ?: null;
    }

    /**
     * Encuentra un contacto por email
     */
    public function findOneByEmail(string $email): ?Contact
    {
        $connection = self::getConnection();
        $sql = "SELECT * FROM contacts WHERE email = :email LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, Contact::class);
        $contact = $stmt->fetch();

        return $contact ?: null;
    }
}
