<?php

namespace App\Enum;

enum BillStatut: int
{
    case CREATED = 1;
    case COMPLETED = 2;
    case CANCEL = 3;
}
