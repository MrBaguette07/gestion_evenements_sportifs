<?php

namespace App\Service;

class DistanceCalculator
{
    /**
     * Calcule la distance en kilomètres entre deux points géographiques
     * en utilisant la formule de Haversine.
     * 
     * @param float $lat1 Latitude du premier point
     * @param float $lon1 Longitude du premier point
     * @param float $lat2 Latitude du second point
     * @param float $lon2 Longitude du second point
     * 
     * @return float La distance en kilomètres
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Rayon de la Terre en kilomètres
        $earthRadius = 6371;

        // Conversion des degrés en radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Différences entre les latitudes et longitudes
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        // Formule de Haversine
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) * 
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Retourne la distance en kilomètres
        return $earthRadius * $c;
    }
}
