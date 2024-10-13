<?php
namespace App\Service;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UserNotFoundException;
use App\Exception\ReservationNotFoundException;

class ReservationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isCarAvailable(int $carId, \DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        $existingReservations = $this->entityManager->getRepository(Reservation::class)->findExistingReservations($carId, $startDate, $endDate);
        return count($existingReservations) === 0;
    }

    public function validateReservationDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        if ($endDate < $startDate) {
            throw new \InvalidArgumentException('The end date must not precede the start date.');
        }
    }

    public function persist(Reservation $reservation): void
    {
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    }

    public function getUser(int $userId): User
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new UserNotFoundException('User not found.');
        }
        return $user;
    }

    public function getReservationById(int $reservationId): Reservation
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($reservationId);
        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found.');
        }
        return $reservation;
    }

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

        // Add any other properties you wish to update...
    }
}
