<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use App\State\ReservationStateProcessor;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[IsGranted('edit','delete','view', 'this')]
#[ApiResource(
    security: "is_granted('ROLE_USER')",
    operations: [
        new Get(),
        new GetCollection( ),
        new Post(processor: ReservationStateProcessor::class),
        new Put(
            uriTemplate: '/reservations/{id}',
            requirements: ['id' => '\d+'],
            processor: ReservationStateProcessor::class
        ),
        new Delete(
            securityPostDenormalize: "object.user == user",
            securityPostDenormalizeMessage: 'Sorry, but you are not the actual book owner.'
        ),        
        ]
)]
#[ApiFilter(SearchFilter::class, properties : ["user" => "exact"])]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ApiProperty]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(groups: ['reservation:post','reservation:write'])]
    private ?\DateTimeInterface $startDate = null;

    #[ApiProperty]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(groups: ['reservation:post'])]
    private ?\DateTimeInterface $endDate = null;

    #[ApiProperty]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $status = null;

    #[ApiProperty]
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
