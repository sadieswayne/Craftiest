<?php

namespace App\Services\Credits;

use App\Services\Credits\Move\MoveDefaultEngineCredits;
use App\Services\Credits\Move\MoveDefaultEntityCredits;

class CreditsService
{
    use MoveDefaultEngineCredits;
    use MoveDefaultEntityCredits;

    public function __construct()
    {
        // do logic here
    }
}
