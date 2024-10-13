<?php


namespace App\tests\Unit\State;

use App\Entity\Reservation;
use App\Entity\User;
use App\Service\CarAvailabilityService;
use App\State\ReservationStateProcessor;
use ApiPlatform\Metadata\Operation;
use Symfony\Bundle\SecurityBundle\Security;
use PHPUnit\Framework\TestCase;

class ReservationStateProcessorUnitTest extends TestCase
{
    private $carAvailabilityService;
    private $security;
    private ReservationStateProcessor $processor;

    protected function setUp(): void
    {
        $this->carAvailabilityService = $this->createMock(CarAvailabilityService::class);
        $this->security = $this->createMock(Security::class);
        $this->processor = new ReservationStateProcessor($this->security, $this->carAvailabilityService);
    }

    public function testProcessValidReservation(): void
    {
        $user = new User();
        $reservation = new Reservation();
        $reservation->setCar(new Car()); // Assuming you have a Car entity
        $operation = new Operation(); // Create a mock or actual Operation as needed

        $this->security->method('getUser')->willReturn($user);
        $this->carAvailabilityService->method('isCarAvailable')->willReturn(true);
        $this->carAvailabilityService->method('validateReservationDates')->willReturn(null);
        $this->carAvailabilityService->method('persist')->willReturn(null);

        $result = $this->processor->process($reservation, $operation);

        $this->assertSame($reservation, $result);
    }

    public function testProcessThrowsExceptionForUnauthenticatedUser(): void
    {
        $this->security->method('getUser')->willReturn(null);
        $reservation = new Reservation();
        $operation = new Operation();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No authenticated user found.');

        $this->processor->process($reservation, $operation);
    }
}
