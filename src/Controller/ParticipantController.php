<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParticipantController extends AbstractController
{
    #[Route('/events/{eventId}/participants/new', name: 'add_participant', requirements: ['eventId' => '\d+'])]
    public function addParticipant(
        int $eventId,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): Response {
        $event = $entityManager->getRepository(Event::class)->find($eventId);
    
        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }
    
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
    
            $participant = new Participant();
            $participant->setName($name);
            $participant->setEmail($email);
            $participant->setEvent($event);
    
            $errors = $validator->validate($participant);
    
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
    
                return $this->redirectToRoute('add_participant', ['eventId' => $eventId]);
            }
    
            $entityManager->persist($participant);
            $entityManager->flush();
    
            $this->addFlash('success', 'Participant ajouté avec succès !');
            return $this->redirectToRoute('view_event', ['id' => $eventId]);
        }
    
        return $this->render('participant/new.html.twig', [
            'event' => $event,
        ]);
    }
}
