<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * Guarda un contacto en la base de datos.
     */
    public function save(Contact $contact, bool $flush = true): void
    {
//        var_dump($contact);die();

        $em = $this->getEntityManager();
        $em->persist($contact);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Elimina un contacto de la base de datos.
     */
    public function remove(Contact $contact, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($contact);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Lista todos los contactos ordenados por nombre
     */
    public function list(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra contactos por nombre (contiene).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra un contacto por email.
     */
    public function findOneByEmail(string $email): ?Contact
    {
        return $this->findOneBy(['email' => $email]);
    }
}
