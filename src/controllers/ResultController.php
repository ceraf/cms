<?php

namespace App\Controllers;

use App\Response;
use App\Controller;

class ResultController extends Controller
{
	public function index()
	{
        $em = $this->getEntityManager();
        $params = ['results' => $em->getRepository('\App\Models\Result')->findAll()];
        return new Response('result/index', $params);
    }
}
