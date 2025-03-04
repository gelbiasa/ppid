<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

trait TraitsController
{
    use AuthorizesRequests, ValidatesRequests, BaseControllerFunction;
}