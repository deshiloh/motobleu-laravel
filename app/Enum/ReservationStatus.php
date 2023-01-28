<?php

namespace App\Enum;

enum ReservationStatus: int
{
    case Created = 1;
    case Canceled = 2;
    case CanceledToPay = 3;
    case Confirmed = 4;
    case Billed = 5;
}
