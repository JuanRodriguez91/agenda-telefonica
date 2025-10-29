<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Response;
use App\Utils\UserSession;

class UserController extends ApiJsonAbstractController
{
    private UserRepository $userRepository;
    private UserSession $userSession;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->userSession = new UserSession();
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->json(['message' => 'JSON no válido'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['email'], $data['password'])) {
            $this->json(['message' => 'Datos inválidos'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findByEmail($data['email']);

        if ($user instanceof User && $user->isEnabled()) {
            if (password_verify($data['password'], $user->getPassword())) {
                $this->userSession->login($user->getId());
                $this->json([
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'createdAt' => $user->getCreatedAt(),
                    'updatedAl' => $user->getUpdatedAt(),
                ]);
            }
        }

        $this->json(['message' => 'Usuario y/o contraseña incorrecto'], Response::HTTP_UNAUTHORIZED);

    }

    public function logout(): void
    {
        $this->userSession->logout();
        $this->json(['message' => 'Sesión cerrada']);
    }

    public function getLogged(): void
    {
        if ($this->userSession->isLoggedIn()) {
            $userId = $this->userSession->getUser();
            $user = $this->userRepository->findById((int)$userId);

            $this->json([
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'enabled' => $user->isEnabled(),
                'createdAt' => $user->getCreatedAt(),
                'updatedAt' => $user->getUpdatedAt(),
            ]);
        }

        $this->json([
            'message' => 'Usuario no logado',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function create(): void
    {
        // Insertamos usuario de prueba
        // todo: en una app completa insertaríamos los datos que nos pasaran por POST
        $user = new User();
        $user->setName('Admin')
            ->setEmail('admin@admin.com')
            ->setPassword('admin123')
            ->setEnabled(true)
            ->setCreatedAt();

        $this->userRepository->save($user);

        $this->json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'enabled' => $user->isEnabled(),
            'createdAt' => $user->getCreatedAt(),
            'updatedAt' => $user->getUpdatedAt(),
        ]);
    }
}