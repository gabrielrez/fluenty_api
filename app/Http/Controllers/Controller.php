<?php

namespace App\Http\Controllers;

use App\Traits\Respondable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use Respondable, AuthorizesRequests;
}
