<?php

namespace App\Enums;

enum ReturnOderStatus: string
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case DAMAGED = 'Damaged';
    case MISMATCH = 'Mismatch';
    // case REJECTED = 'Rejected';
}
