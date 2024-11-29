<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DistanceCalculator;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/events', name: 'list_events')]
    public function listEvents(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'view_event', requirements: ['id' => '\d+'])]
    public function viewEvent(int $id, EntityManagerInterface $entityManager): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement demandé n\'existe pas.');
        }

        return $this->render('event/view.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/{id}/distance', name: 'calculate_distance', requirements: ['id' => '\d+'])]
    public function calculateDistanceToEvent(
        int $id,
        Request $request,
        DistanceCalculator $distanceCalculator,
        EntityManagerInterface $entityManager
    ): Response {
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        $userLat = $request->query->get('lat');
        $userLon = $request->query->get('lon');

        if (!$userLat || !$userLon) {
            return new Response('Veuillez fournir les paramètres "lat" et "lon".', 400);
        }

        [$eventLat, $eventLon] = $this->getCoordinatesFromLocation($event->getLocation());

        $distance = $distanceCalculator->calculateDistance(
            (float) $userLat,
            (float) $userLon,
            $eventLat,
            $eventLon
        );

        return new Response(sprintf('Distance jusqu\'à l\'événement : %.2f km', $distance));
    }

    private function getCoordinatesFromLocation(string $location): array
    {
        return [48.8566, 2.3522];
    }

    #[Route('/events/new', name: 'add_event')]
    public function addEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $dateInput = $request->request->get('date');
            $location = $request->request->get('location');

            // Valider et traiter la date
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateInput);

            // Vérifications de validation
            if (!$name || !$date || !$location) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('add_event');
            }

            if ($date < new \DateTime()) {
                $this->addFlash('error', 'La date ne peut pas être dans le passé.');
                return $this->redirectToRoute('add_event');
            }

            // Créer un nouvel événement
            $event = new Event();
            $event->setName($name);
            $event->setDate($date);
            $event->setLocation($location);

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('list_events');
        }

        return $this->render('event/add.html.twig');
    }
}
