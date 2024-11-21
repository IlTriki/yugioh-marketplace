<?php
namespace App\Entity;

enum ProductStatus:String {
    case AVAILABLE = "Disponible";
    case OUT_OF_STOCK = "En rupture";
    case PRE_ORDER = "En précommande";
}