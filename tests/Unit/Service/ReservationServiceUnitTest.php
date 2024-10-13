<?php

namespace App\tests\Unit\Service;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReservationServiceUnitTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ReservationRepository $reservationRepository;
    private ReservationService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->service = new ReservationService($this->entityManager, $this->reservationRepository);
    }

    public function testIsCarAvailableReturnsTrue(): void
    {
        $this->reservationRepository->method('findExistingReservations')->willReturn([]);

        $isAvailable = $this->service->isCarAvailable(1, new \DateTime('2023-10-01'), new \DateTime('2023-10-05'));
        $this->assertTrue($isAvailable);
    }

    public function testValidateReservationDatesThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The end date must not precede the start date.');

        $this->service->validateReservationDates(new \DateTime('2023-10-05'), new \DateTime('2023-10-01'));
    }

}
