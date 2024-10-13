<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Reservation;
use App\Entity\User;
use App\Service\ReservationService;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ReservationStateProcessor implements ProcessorInterface
{
    private Security $security;
    private ReservationService $reservationService;

    public function __construct(Security $security, ReservationService $reservationService)
    {
        $this->security = $security;
        $this->reservationService = $reservationService;
    }

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

    private function setUserForReservation(Reservation $data): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Exception('No authenticated user found.');
        }
        $data->setUser($user);
    }

    private function isPutOperation(Operation $operation, array $uriVariables): bool
    {
        return $operation->getName() === '_api_/reservations/{id}_put' && isset($uriVariables['id']);
    }

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

    private function validateReservationDates(Reservation $data): void
    {
        $this->reservationService->validateReservationDates($data->getStartDate(), $data->getEndDate());
    }

    private function checkCarAvailability(Reservation $data): void
    {
        if (!$this->reservationService->isCarAvailable($data->getCar()->getId(), $data->getStartDate(), $data->getEndDate())) {
            throw new \Exception('The car is not available for the selected dates.');
        }
    }

    private function handleDelete(int $reservationId): void
    {
        $reservation = $this->reservationService->getReservationById($reservationId);

        if (!$reservation) {
            throw new ReservationNotFoundException('Reservation not found.');
        }

        $this->authorizeUser($reservation);

        $this->reservationService->removeReservation($reservation);
    }

    private function authorizeUser(Reservation $reservation): void
    {
        $user = $this->security->getUser();
        if ($reservation->getUser() !== $user) {
            throw new \Exception('You do not have permission to cancel this reservation.');
        }
    }
}
