<?php

namespace App\Controllers;

use App\Response;
use App\Controller;

class IndexController extends Controller
{
	public function index()
	{
		return new Response('index/index');
	}
}
