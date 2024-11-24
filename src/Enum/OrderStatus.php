<?php
namespace App\Enum;

enum OrderStatus:String {
    case SENDING = "En cours";
    case DELIVERED = "Envoyé";
}