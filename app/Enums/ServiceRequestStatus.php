<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case Queued = 'queued';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Rejected = 'rejected';
}
