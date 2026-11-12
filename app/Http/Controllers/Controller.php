<?php

namespace App\Http\Controllers;

use App\Traits\ResolvesSchoolId;

abstract class Controller
{
    use ResolvesSchoolId;
}
