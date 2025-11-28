<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Mengimpor Base Controller Framework

abstract class Controller extends BaseController // Mewarisi metode middleware() dari BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}