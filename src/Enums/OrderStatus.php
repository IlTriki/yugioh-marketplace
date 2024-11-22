<?php
namespace App\Entity;

enum OrderStatus:String {
    case EnCours = "En cours";
    case Envoye = "Envoyé";
}