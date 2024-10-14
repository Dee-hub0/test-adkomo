<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Reservation;
use App\Entity\User;
use App\Service\ReservationService;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class ReservationStateProcessor
 *
 * Processes reservation state changes, handling the creation, update, and validation
 * of reservations.
 */
class ReservationStateProcessor implements ProcessorInterface
{
    private Security $security;
    private ReservationService $reservationService;

    /**
     * Constructor for ReservationStateProcessor.
     *
     * @param Security $security The security service to manage user authentication.
     * @param ReservationService $reservationService The service for handling reservations.
     */
    public function __construct(Security $security, ReservationService $reservationService)
    {
        $this->security = $security;
        $this->reservationService = $reservationService;
    }

    /**
     * Processes a reservation data input for creating or updating a reservation.
     *
     * @param mixed $data The reservation data to be processed.
     * @param Operation $operation The operation being performed (create or update).
     * @param array $uriVariables Variables from the URI (e.g., reservation ID).
     * @param array $context Contextual information for the operation.
     *
     * @return Reservation|null The processed reservation entity or null on failure.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?Reservation
    {
        $this->setUserForReservation($data);

        if ($this->isPutOperation($operation, $uriVariables)) {
            $data = $this->handlePutOperation($data, $uriVariables);
        }

        $this->validateReservationDates($data);
        $this->checkCarAvailability($data);
        $this->reservationService->persist($data);

        return $data;
    }

    /**
     * Sets the authenticated user for the reservation.
     *
     * @param Reservation $data The reservation entity to set the user for.
     *
     * @throws \Exception if no authenticated user is found.
     */
    private function setUserForReservation(Reservation $data): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Exception('No authenticated user found.');
        }
        $data->setUser($user);
    }

    /**
     * Checks if the current operation is a PUT operation for updating a reservation.
     *
     * @param Operation $operation The operation being performed.
     * @param array $uriVariables Variables from the URI.
     *
     * @return bool True if it is a PUT operation, false otherwise.
     */
    private function isPutOperation(Operation $operation, array $uriVariables): bool
    {
        return $operation->getName() === '_api_/reservations/{id}_put' && isset($uriVariables['id']);
    }

    /**
     * Handles the processing of a PUT operation, updating an existing reservation.
     *
     * @param Reservation $data The reservation data to update.
     * @param array $uriVariables Variables from the URI (e.g., reservation ID).
     *
     * @return mixed The updated reservation data.
     *
     * @throws \Exception if the reservation is not found.
     */
    private function handlePutOperation(Reservation $data, array $uriVariables): mixed
    {
        $existingReservation = $this->reservationService->getReservationById($uriVariables['id']);

        if (!$existingReservation) {
            throw new \Exception('Reservation not found.');
        }

        // Preserve existing dates if not provided
        if ($data->getStartDate() === null) {
            $data->setStartDate($existingReservation->getStartDate());
        }

        if ($data->getEndDate() === null) {
            $data->setEndDate($existingReservation->getEndDate());
        }

        // Update existing reservation properties
        $this->reservationService->updateReservationFromData($existingReservation, $data);
        $data = $existingReservation; // Update the data variable
        return $data;
    }

    /**
     * Validates the start and end dates of the reservation.
     *
     * @param Reservation $data The reservation data to validate.
     */
    private function validateReservationDates(Reservation $data): void
    {
        $this->reservationService->validateReservationDates($data->getStartDate(), $data->getEndDate());
    }

    /**
     * Checks if the car associated with the reservation is available for the specified dates.
     *
     * @param Reservation $data The reservation data containing the car and dates.
     *
     * @throws \Exception if the car is not available.
     */
    private function checkCarAvailability(Reservation $data): void
    {
        if (!$this->reservationService->isCarAvailable($data->getCar()->getId(), $data->getStartDate(), $data->getEndDate())) {
            throw new \Exception('The car is not available for the selected dates.');
        }
    }

    /**
     * Handles the deletion of a reservation by its ID.
     *
     * @param int $reservationId The ID of the reservation to delete.
     *
     * @throws ReservationNotFoundException if the reservation is not found.
     */
    private function handleDelete(int $reservationId): void
    {
        $reservation = $this->reservationService->getReservationById($reservationId);

        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found.');
        }

        $this->authorizeUser($reservation);

        $this->reservationService->removeReservation($reservation);
    }

    /**
     * Authorizes the user to perform actions on the specified reservation.
     *
     * @param Reservation $reservation The reservation to check authorization against.
     *
     * @throws \Exception if the user is not authorized to cancel the reservation.
     */
    private function authorizeUser(Reservation $reservation): void
    {
        $user = $this->security->getUser();
        if ($reservation->getUser() !== $user) {
            throw new \Exception('You do not have permission to cancel this reservation.');
        }
    }
}