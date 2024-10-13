<?php
// src/Security/Voter/ReservationVoter.php

namespace App\Security\Voter;

use App\Entity\Reservation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ReservationVoter extends Voter
{
    // Define your actions
    public const DELETE = 'reservation_delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is one of the defined actions and if the subject is a Reservation
        return $attribute === self::DELETE && $subject instanceof Reservation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // If the user is anonymous, deny access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Reservation $reservation */
        $reservation = $subject;

        switch ($attribute) {
            case self::DELETE:
                // Allow deletion if the user is the owner of the reservation
                return $reservation->getUser() === $user; // Assuming getUser() returns the owner of the reservation
        }

        return false;
    }
}