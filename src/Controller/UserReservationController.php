<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[AsController]
class UserReservationController extends AbstractController
{
    private ReservationRepository $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    #[Route(
        name: 'get_user_reservations',
        path: '/api/users/{id}/reservations',
        methods: ['GET']
    )]
    public function __invoke(int $id): JsonResponse
    {
        // Check if the user is authenticated
        $user = $this->getUser();
        if (!$user || $user->getId() !== $id) {
            throw new AccessDeniedException('You do not have permission to access these reservations.');
        }

        // Fetch reservations for the user
        $reservations = $this->reservationRepository->findBy(['user' => $user]);

        if (empty($reservations)) {
            return $this->json(['message' => 'No reservations found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($reservations);
    }
}