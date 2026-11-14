<?php

namespace App\Http\Controllers\Staf;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class RequestController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Staf/Requests/Index');
    }
}

