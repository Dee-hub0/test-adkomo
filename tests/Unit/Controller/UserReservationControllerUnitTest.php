<?php

namespace App\Tests\Unit\Controller;

use App\Entity\User;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserReservationControllerUnitTest extends WebTestCase
{
    private $client;
    private $reservationRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->reservationRepository = $this->createMock(ReservationRepository::class);
        
        // Mock the repository in the service container
        $this->client->getContainer()->set(ReservationRepository::class, $this->reservationRepository);
    }

    public function testAccessDeniedForUnauthenticatedUser()
    {
        $this->client->request('GET', '/api/users/1/reservations');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testNoReservationsFound()
    {
        // Simulate login for user username 1
        $this->logInUser(1);
        
        // Simulate the reservation repository returning no reservations
        $this->reservationRepository
            ->method('findBy')
            ->willReturn([]);

        $this->client->request('GET', '/api/users/1/reservations');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains(['message' => 'No reservations found.']);
    }

    public function testReservationsFound()
    {
        // Simulate login for user username 1
        $this->logInUser(1);
        
        // Simulate the reservation repository returning some reservations
        $reservations = [
            ['id' => 1, 'user' => ['id' => 1], 'date' => '2023-10-01'],
            ['id' => 2, 'user' => ['id' => 1], 'date' => '2023-10-02'],
        ];

        $this->reservationRepository
            ->method('findBy')
            ->willReturn($reservations);

        $this->client->request('GET', '/api/users/1/reservations');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains($reservations);
    }

    private function logInUser(int $userId): void
    {
        // Create a user and simulate authentication
        $user = new User();
        $user->setUsername('testuser'); // or any other property needed
        // Set additional properties as necessary

        $this->client->loginUser($user);
    }
}
