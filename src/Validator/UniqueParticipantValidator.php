<?php

namespace App\Validator;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueParticipantValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueParticipant) {
            throw new \InvalidArgumentException(sprintf('Invalid constraint type: %s', get_class($constraint)));
        }

        $existingParticipant = $this->entityManager->getRepository(Participant::class)
            ->findOneBy(['email' => $value, 'event' => $this->context->getObject()->getEvent()]);

        if ($existingParticipant) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
        }
    }
}
