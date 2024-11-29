<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueParticipant extends Constraint
{
    public string $message = 'Le participant avec l\'email "{{ email }}" est déjà inscrit à cet événement.';
}
