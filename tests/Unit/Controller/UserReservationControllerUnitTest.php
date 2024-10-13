<?php

namespace App\tests\Unit\Controller;

use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Controller\UserReservationController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserReservationControllerUnitTest extends WebTestCase
{
    private ReservationRepository $reservationRepository;
    private UserReservationController $controller;

    protected function setUp(): void
    {
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        $this->controller = new UserReservationController($this->reservationRepository);
    }

    public function testInvokeReturnsReservations(): void
    {
        $user = new User();
        $this->controller->setUser($user); // Assuming setUser method exists for test context

        $this->reservationRepository->method('findBy')->willReturn([]);

        $response = $this->controller->__invoke(1);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testInvokeThrowsAccessDeniedException(): void
    {
        $user = new User();
        $this->controller->setUser($user);

        $this->expectException(AccessDeniedException::class);
        $this->controller->__invoke(1);
    }

    // Add more tests for edge cases...
}
