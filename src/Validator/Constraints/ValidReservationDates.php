<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidReservationDates extends Constraint
{
    public $message = 'The end date must be after the start date.';
}
