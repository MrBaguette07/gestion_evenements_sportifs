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
    #[Route('/events/{id}/participants/new', name: 'add_participant', methods: ['POST', 'GET'])]
    public function addParticipant(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');

            $participant = new Participant();
            $participant->setName($name);
            $participant->setEmail($email);
            $participant->setEvent($event);

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Participant ajouté avec succès !');

            return $this->redirectToRoute('view_event', ['id' => $id]);
        }

        return $this->render('participant/add_participant.html.twig', [
            'event' => $event,
        ]);
    }
}
