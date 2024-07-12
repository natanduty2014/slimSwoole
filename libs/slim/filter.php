<?php

namespace Functions\slim;

class filter{

    static public function minutesToHours($minutes){
        // Calcula as horas e minutos
        $horas = floor($minutes / 60);
        $minutosRestantes = $minutes % 60;

        // Formata a saída
        $formatoHM = sprintf('%02d:%02d', $horas, $minutosRestantes);

        return $formatoHM;
    }
}