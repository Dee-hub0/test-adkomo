<?php

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Car;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testGetId()
    {
        $reservation = new Reservation();
        $this->assertNull($reservation->getId());
    }

    public function testStartDate()
    {
        $reservation = new Reservation();
        $startDate = new \DateTime('2023-10-01 10:00:00');
        $reservation->setStartDate($startDate);

        $this->assertEquals($startDate, $reservation->getStartDate());
    }

    public function testEndDate()
    {
        $reservation = new Reservation();
        $endDate = new \DateTime('2023-10-01 12:00:00');
        $reservation->setEndDate($endDate);

        $this->assertEquals($endDate, $reservation->getEndDate());
    }

    public function testStatus()
    {
        $reservation = new Reservation();
        $reservation->setStatus('confirmed');

        $this->assertEquals('confirmed', $reservation->getStatus());
    }

    public function testCar()
    {
        $reservation = new Reservation();
        $car = new Car(); // Assuming you have a Car entity.
        $reservation->setCar($car);

        $this->assertSame($car, $reservation->getCar());
    }

    public function testUser()
    {
        $reservation = new Reservation();
        $user = new User(); // Assuming you have a User entity.
        $reservation->setUser($user);

        $this->assertSame($user, $reservation->getUser());
    }
}
