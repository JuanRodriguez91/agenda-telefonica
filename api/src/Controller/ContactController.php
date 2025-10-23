<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/contacts', name: 'api_contacts_')]
class ContactController extends AbstractController
{
    public function __construct(private ContactRepository $contactRepository) {}

    /**
     * 🟢 GET /contacts
     * Lista todos los contactos
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $contacts = $this->contactRepository->list();

        $data = array_map(fn(Contact $c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'phone' => $c->getPhone(),
            'email' => $c->getEmail(),
            'createdAt' => $c->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $c->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], $contacts);

        return $this->json($data);
    }

    /**
     * 🟢 GET /contacts/{id}
     * Muestra un contacto por ID
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $contact = $this->contactRepository->find($id);

        if (!$contact) {
            return $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'phone' => $contact->getPhone(),
            'email' => $contact->getEmail(),
            'createdAt' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $contact->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 🟢 POST /contacts
     * Crea un nuevo contacto
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'], $data['phone'], $data['email'])) {
            return $this->json(['message' => 'Datos inválidos'], Response::HTTP_BAD_REQUEST);
        }

        // Validación de email único
        if ($this->contactRepository->findOneByEmail($data['email'])) {
            return $this->json(['message' => 'El email ya existe'], Response::HTTP_CONFLICT);
        }

        $contact = (new Contact())
            ->setName($data['name'])
            ->setPhone($data['phone'])
            ->setEmail($data['email']);

        $this->contactRepository->save($contact);

        return $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'email' => $contact->getEmail(),
            'phone' => $contact->getPhone(),
            'createdAt' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $contact->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_CREATED);
    }

    /**
     * 🟢 PUT /contacts/{id}
     * Actualiza un contacto existente
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            return $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['message' => 'JSON no válido'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $contact->setName($data['name']);
        }
        if (isset($data['phone'])) {
            $contact->setPhone($data['phone']);
        }
        if (isset($data['email'])) {
            // Verificamos que el nuevo email no esté en uso
            $existing = $this->contactRepository->findOneByEmail($data['email']);
            if ($existing && $existing->getId() !== $contact->getId()) {
                return $this->json(['message' => 'El email ya existe'], Response::HTTP_CONFLICT);
            }
            $contact->setEmail($data['email']);
        }

        $this->contactRepository->save($contact);

        return $this->json([
            'id' => $contact->getId(),
            'name' => $contact->getName(),
            'email' => $contact->getEmail(),
            'phone' => $contact->getPhone(),
            'createdAt' => $contact->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $contact->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], Response::HTTP_OK);
    }

    /**
     * 🟢 DELETE /contacts/{id}
     * Elimina un contacto
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            return $this->json(['message' => 'Contacto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->contactRepository->remove($contact);

        return $this->json(['message' => 'Contacto borrado correctamente'], Response::HTTP_OK);
    }
}
