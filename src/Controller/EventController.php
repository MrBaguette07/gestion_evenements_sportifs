<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DistanceCalculator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class EventController extends AbstractController
{
    private $distanceCalculator;
    private $doctrine;

    public function __construct(DistanceCalculator $distanceCalculator, ManagerRegistry $doctrine)
    {
        $this->distanceCalculator = $distanceCalculator;
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/events', name: 'list_events')]
    public function listEvents(): Response
    {
        // Utilisation du service Doctrine pour récupérer tous les événements
        $events = $this->doctrine->getRepository(Event::class)->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/{id}', name: 'view_event', requirements: ['id' => '\d+'])]
    public function viewEvent(int $id): Response
    {
        // Récupérer l'événement avec Doctrine
        $event = $this->doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        return $this->render('event/view.html.twig', [
            'event' => $event,
        ]);
    }



    #[Route('/events/{id}/distance', name: 'event_distance', methods: ['GET'])]
    public function calculateDistanceToEvent(int $id, Request $request, DistanceCalculator $distanceCalculator): JsonResponse
    {
        $event = $this->doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $userLat = $request->query->get('lat');
        $userLon = $request->query->get('lon');

        if (!is_numeric($userLat) || !is_numeric($userLon)) {
            return $this->json(['error' => 'Les coordonnées doivent être valides.'], 400);
        }

        // Calculer la distance
        $distance = $distanceCalculator->calculateDistance(
            $userLat, $userLon, $event->getLatitude(), $event->getLongitude()
        );

        // Retourner la distance en JSON
        return $this->json([
            'distance' => $distance,
            'event' => $event->getName(),
            'location' => $event->getLocation()
        ]);
    }


    private function getCoordinatesFromLocation(string $location): array
    {
        return [48.8566, 2.3522]; // Example: returns the coordinates of Paris
    }

    #[Route('/events/new', name: 'add_event')]
    public function addEvent(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $dateInput = $request->request->get('date');
            $location = $request->request->get('location');
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');

            // Valider et traiter la date
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateInput);

            // Vérifications de validation
            if (!$name || !$date || !$location || !$latitude || !$longitude) {
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
            $event->setLatitude($latitude);
            $event->setLongitude($longitude);

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès !');
            return $this->redirectToRoute('list_events');
        }

        return $this->render('event/add.html.twig');
    }

    #[Route('/events/{id}/delete', name: 'delete_event')]
    public function deleteEvent(int $id, EntityManagerInterface $entityManager): Response
    {
        $event = $this->doctrine->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'Événement supprimé avec succès');
        return $this->redirectToRoute('list_events');
    }

    #[Route('/events/{id}/edit', name: 'edit_event')]
    public function editEvent(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'événement à modifier
        $event = $this->doctrine->getRepository(Event::class)->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Vérifier si le formulaire est soumis
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $dateInput = $request->request->get('date');
            $location = $request->request->get('location');
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');

            // Valider et traiter la date
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateInput);

            // Vérifications de validation
            if (!$name || !$date || !$location || !$latitude || !$longitude) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('edit_event', ['id' => $id]);
            }

            // Mettre à jour les informations de l'événement
            $event->setName($name);
            $event->setDate($date);
            $event->setLocation($location);
            $event->setLatitude($latitude);
            $event->setLongitude($longitude);

            // Sauvegarder les changements dans la base de données
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement mis à jour avec succès !');
            return $this->redirectToRoute('list_events');
        }

        // Renvoyer la vue avec l'événement existant
        return $this->render('event/edit.html.twig', [
            'event' => $event,
        ]);
    }





}
