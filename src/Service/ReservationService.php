<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UserNotFoundException;
use App\Exception\ReservationNotFoundException;

/**
 * Service class for managing reservations.
 */
class ReservationService
{
    private EntityManagerInterface $entityManager;

    /**
     * Constructor for ReservationService.
     *
     * @param EntityManagerInterface $entityManager The entity manager to handle database operations.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checks if a car is available for reservation during the specified dates.
     * 
     * @param int $carId The ID of the car to check for availability.
     * @param \DateTimeInterface $startDate The start date of the desired reservation.
     * @param \DateTimeInterface $endDate The end date of the desired reservation.
     *
     * @return bool True if the car is available, false otherwise.
     */
    public function isCarAvailable(int $carId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        $existingReservations = $this->entityManager->getRepository(Reservation::class)->findExistingReservations($carId, $startDate, $endDate);
        return count($existingReservations) === 0;
    }

    /**
     * Validates the reservation dates to ensure the end date is not before the start date.
     *
     * @param \DateTimeInterface $startDate The start date of the reservation.
     * @param \DateTimeInterface $endDate The end date of the reservation.
     *
     * @throws \InvalidArgumentException if the end date precedes the start date.
     */
    public function validateReservationDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($endDate < $startDate) {
            throw new \InvalidArgumentException('The end date must not precede the start date.');
        }
    }

    /**
     * Persists a reservation entity to the database.
     *
     * @param Reservation $reservation The reservation entity to be persisted.
     */
    public function persist(Reservation $reservation): void
    {
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    }

    /**
     * Retrieves a user by their ID.
     *
     * If the user is not found, a UserNotFoundException is thrown.
     *
     * @param int $userId The ID of the user to retrieve.
     *
     * @return User The found user entity.
     *
     * @throws UserNotFoundException if the user with the given ID does not exist.
     */
    public function getUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new UserNotFoundException('User not found.');
        }
        return $user;
    }

    /**
     * Retrieves a reservation by its ID.
     *
     * If the reservation is not found, a ReservationNotFoundException is thrown.
     *
     * @param int $reservationId The ID of the reservation to retrieve.
     *
     * @return Reservation The found reservation entity.
     *
     * @throws ReservationNotFoundException if the reservation with the given ID does not exist.
     */
    public function getReservationById(int $reservationId): Reservation
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($reservationId);
        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found.');
        }
        return $reservation;
    }

    /**
     * Updates an existing reservation with new data.
     *
     * @param Reservation $existingReservation The reservation to be updated.
     * @param Reservation $data The reservation data containing new values.
     */
    public function updateReservationFromData(Reservation $existingReservation, Reservation $data): void
    {
        // Update properties explicitly
        if ($data->getStartDate() !== null) {
            $existingReservation->setStartDate($data->getStartDate());
        }

        if ($data->getEndDate() !== null) {
            $existingReservation->setEndDate($data->getEndDate());
        }

        if ($data->getCar() !== null) {
            $existingReservation->setCar($data->getCar());
        }
    }
}