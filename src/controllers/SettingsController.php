<?php

namespace App\Controllers;

use App\Response;
use App\Controller;

use App\models\Question;

class SettingsController extends Controller
{
	public function index()
	{
        $em = $this->getEntityManager();
        $errors = null;
        $success = null;

        if ($this->request->isPost()) {
            $min = (int)$this->request->getPost('min');
            $max = (int)$this->request->getPost('max');
            
            if (($min < 0) || ($min > 100))
                $errors[] = 'Минимальная сложность должна быть в диапазоне от 0 до 100.';
            if (($max < 0) || ($max > 100))
                $errors[] = 'Максимальная сложность должна быть в диапазоне от 0 до 100.';
            if ($min >= $max)
                $errors[] = 'Максимальная сложность должна больше минимальной.';

            if (!$errors) {
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
            }
        }
        
        $settings = $em->getRepository('\App\Models\Setting')->findBy([]);
        foreach ($settings as $item) {
            $params['settings'][$item->getTitle()] = $item;
        }
        
        $params['errors'] = $errors;
        $params['success'] = $success;

        
		return new Response('settings/index', $params);
	}
}
