<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use App\Entity\Contact;
use App\Utils\Response;

class ContactController extends ApiJsonAbstractController
{
    private ContactRepository $contactRepository;

    public function __construct()
    {
        parent::__construct();
        $this->contactRepository = new ContactRepository();
    }

    /**
     * 🟢 GET /contacts
     * Lista todos los contactos
     */
    public function list(): void
    {
        $contacts = $this->contactRepository->list();

        $data = array_map(fn(Contact $c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'phone' => $c->getPhone(),
            'email' => $c->getEmail(),
            'createdAt' => $c->getCreatedAt(),
            'updatedAt' => $c->getUpdatedAt(),
        ], $contacts);

        $this->json($data);
    }

    /**
     * 🟢 GET /contacts/{id}
     * Muestra un contacto por ID
     */
    public function show(int $id): void
    {
        $contact = $this->contactRepository->find($id);

        if (!$contact) {
            $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'phone' => $contact->getPhone(),
            'email' => $contact->getEmail(),
            'createdAt' => $contact->getCreatedAt(),
            'updatedAt' => $contact->getUpdatedAt(),
        ]);
    }

    /**
     * 🟢 POST /contacts
     * Crea un nuevo contacto
     */
    public function create(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['name'], $data['phone'], $data['email'])) {
            $this->json(['message' => 'Datos inválidos'], Response::HTTP_BAD_REQUEST);
        }

        if ($this->contactRepository->findOneByEmail($data['email'])) {
            $this->json(['message' => 'El email ya existe'], Response::HTTP_CONFLICT);
        }

        $contact = (new Contact())
            ->setName($data['name'])
            ->setPhone($data['phone'])
            ->setEmail($data['email'])
            ->setCreatedAt();

        $this->contactRepository->save($contact);

        $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'phone' => $contact->getPhone(),
            'email' => $contact->getEmail(),
            'createdAt' => $contact->getCreatedAt(),
            'updatedAt' => $contact->getUpdatedAt(),
        ], Response::HTTP_CREATED);
    }

    /**
     * 🟢 PUT /contacts/{id}
     * Actualiza un contacto existente
     */
    public function update(int $id): void
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->json(['message' => 'JSON no válido'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $contact->setName($data['name']);
        }
        if (isset($data['phone'])) {
            $contact->setPhone($data['phone']);
        }
        if (isset($data['email'])) {
            $existing = $this->contactRepository->findOneByEmail($data['email']);
            if ($existing && $existing->getId() !== $contact->getId()) {
                $this->json(['message' => 'El email ya existe'], Response::HTTP_CONFLICT);
            }
            $contact->setEmail($data['email']);
        }

        $contact->setUpdatedAt();
        $this->contactRepository->save($contact);

        $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'phone' => $contact->getPhone(),
            'email' => $contact->getEmail(),
            'createdAt' => $contact->getCreatedAt(),
            'updatedAt' => $contact->getUpdatedAt(),
        ]);
    }

    /**
     * 🟢 DELETE /contacts/{id}
     * Elimina un contacto
     */
    public function delete(int $id): void
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->contactRepository->remove($contact);

        $this->json(['message' => 'Contacto borrado correctamente']);
    }
}
