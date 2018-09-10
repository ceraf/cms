<?php

namespace App\Controllers;

use App\Response;
use App\Controller;

class NotfoundController extends Controller
{
    public function index()
    {
        return new Response('404/index', '404');
    }
}
