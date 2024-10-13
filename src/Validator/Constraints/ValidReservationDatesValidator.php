<?php

namespace App\Validator\Constraints;

use App\Validator\Constraints\ValidReservationDates;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidReservationDatesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value->getStartDate() >= $value->getEndDate()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
