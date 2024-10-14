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
/**
 * Class UserReservationController
 *
 * Handles user-specific reservation operations like retrieving a user's reservations.
 */
class UserReservationController extends AbstractController
{
    private ReservationRepository $reservationRepository;

    /**
     * Constructor for UserReservationController.
     *
     * @param ReservationRepository $reservationRepository Repository for managing reservations.
     */
    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Handles the retrieval of reservations for a specific user.
     *
     * @param int $id The ID of the user whose reservations are to be fetched.
     *
     * @throws AccessDeniedException if the user is not authenticated or does not match the ID.
     *
     * @return JsonResponse A JSON response containing the user's reservations or a message indicating no reservations were found.
     */
    #[Route(
        name: 'get_user_reservations',
        path: '/api/users/{id}/reservations',
        methods: ['GET']
    )]
    public function __invoke(int $id): JsonResponse
    {
        // Check if the user is authenticated
        $user = $this->getUser();
        
        // Ensure the authenticated user has permission to access the requested user's reservations
        if (!$user || $user->getId() !== $id) {
            throw new AccessDeniedException('You do not have permission to access these reservations.');
        }

        // Fetch reservations for the authenticated user
        $reservations = $this->reservationRepository->findBy(['user' => $user]);

        // If no reservations were found, return a 404 response
        if (empty($reservations)) {
            return $this->json(['message' => 'No reservations found.'], Response::HTTP_NOT_FOUND);
        }

        // Return the list of reservations as a JSON response
        return $this->json($reservations);
    }
}