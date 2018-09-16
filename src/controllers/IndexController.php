<?php

namespace App\Controllers;

use App\Response;
use App\Controller;
use App\Models\Parser\Parser;

class IndexController extends Controller
{
	public function index()
	{
        $em = $this->getEntityManager();
        $errors = null;
        $success = null;
        $url = null;

        if ($this->request->isPost()) {
            $url = $this->request->getPost('url');
            if (!$url)
                $errors[] = 'Необходимо указать адрес сайта.';
            elseif (!filter_var($url, FILTER_VALIDATE_URL))
                $errors[] = 'Адрес сайта указан не верно.';

            if (!$errors) {
               // $links = $this->getLinks($url);
                $parser = (new Parser($url))->analysis();
                $badlinks = $parser->getBadLinks();
				$pages = $parser->getPages();

				var_dump($badlinks );
				var_dump($pages );
				
                //var_dump($links );
/*                
				try {
                    $minset = $em->getRepository('\App\Models\Setting')
                        ->findOneBy(['title' => 'min']);
                    $maxset = $em->getRepository('\App\Models\Setting')
                        ->findOneBy(['title' => 'max']);
                        
                    $minset->setValue($min);
                    $maxset->setValue($max);
                    $questions = $em->getRepository('\App\Models\Question')->findAll();
                    
                    $em->getConnection()->beginTransaction();
					$em = $this->getEntityManager();
					$em->persist($minset);
                    $em->persist($maxset);
					$em->flush();
                    foreach($questions as $q) {
                        $q->setComplexity(mt_rand($min,$max));
                        $em->flush();
                    }
                    $em->getConnection()->commit();
					$success = 'Настройки успешно сохранены.';
				} catch (\Exception $e){
                    $em->getConnection()->rollback();
					$errors[] = 'Ошибка базы данных.';
				}
                */
            }
            
        }
        
        $params['errors'] = $errors;
        $params['success'] = $success;
        $params['url'] = $url;
		return new Response('index/index', $params);
	}
	
	public function checkDomain()
    {
        $errors = null;
        $status = null;

        if ($this->isAjax()) {
            $url = $this->request->getPost('url');
            if (!$url)
                $errors[] = 'Необходимо указать адрес сайта.';
            elseif (!filter_var($url, FILTER_VALIDATE_URL))
                $errors[] = 'Адрес сайта указан не верно.';

            if (!$errors) {
				$path = parse_url($url);
				$domain = $path['host'];
				$scheme = $path['scheme'];
				$url= $scheme.'://'.$domain;
                $parser = (new Parser($url))->clearCache()->analysis();
                $badlinks = $parser->getBadLinks();
				$pages = $parser->getPages();
				$this->setSession('pages', $pages);
				$this->setSession('badlinks', $badlinks);
				$data = ['num' => count($pages), 'bad' => $badlinks, 'url' => $url];
				$status = 'success';
            } else {
                $status = 'error';
                $data = $errors;
            }
            echo json_encode(['status' => $status, 'data' => $data]);
            exit;
		}
		return $this->notFound();
	}
	
	public function next()
    {
        $errors = null;
        $status = null;

        if ($this->isAjax()) {
            $stage = (int)$this->request->getPost('stage');
			$prevpages = $this->getSession('pages');
			if (!$prevpages || !isset($prevpages[$stage]))
				$errors[] = 'Такой страницы не существует.';

            if (!$errors) {
				$url = $prevpages[$stage];
				$path = parse_url($url);
				$domain = $path['host'];
				$scheme = $path['scheme'];
                $parser = (new Parser($url))->analysis();
                $badlinks = $parser->getBadLinks();
				$pages = $parser->getPages();
				if ($pages) {
					$pages = array_diff($pages, $prevpages);
					$pages = array_merge($prevpages, $pages);
					$this->setSession('pages', $pages);					
				} else
					$pages = $prevpages;
				$data = ['num' => count($pages), 'bad' => $badlinks, 'url' => $url, 'pages' => $pages];
				$status = 'success';
            } else {
                $status = 'error';
                $data = $errors;
            }
            echo json_encode(['status' => $status, 'data' => $data]);
            exit;
		}
		return $this->notFound();
	}
	
}
