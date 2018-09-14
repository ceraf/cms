<?php

namespace App\Controllers;

use App\Response;
use App\Controller;
use App\GenRand;

use App\models\Question;
use App\models\Result;

class IndexController extends Controller
{
    const NUM_TEST_QUESTIONS = '40';

	public function index()
	{
		return new Response('index/index');
	}
    
    public function test()
    {
        $em = $this->getEntityManager();
        
        $errors = null;
        $level = 0;
        $res = null;

        if ($this->isAjax()) {
            $level = (int)$this->request->getPost('level');
            
            if (($level <= 0) || ($level > 100))
                $errors[] = 'Уровень интеллекта должен быть в диапазоне от 0 до 100.';
            
            if (!$errors) {
                $questionids = $this->getQuestions();
                $minset = $em->getRepository('\App\Models\Setting')
                        ->findOneBy(['title' => 'min']);
                $maxset = $em->getRepository('\App\Models\Setting')
                        ->findOneBy(['title' => 'max']);
                $res = [];
                try {
                    $em->getConnection()->beginTransaction();
                    $done = 0;
					$i = 1;
                    foreach ($questionids as $id) {
                        $question = $em->find('\App\Models\Question', $id);
                        $line['num'] = $question->getNum();
                        $line['id'] = $question->getId();
                        $line['complexity'] = $question->getComplexity();
                        $line['test'] = ($level > $question->getComplexity()) ? 'Верно' : 'Не верно';
						$line['is_success'] = ($level > $question->getComplexity()) ? '1' : '0';
                        if ($line['is_success'])
                            $done++;
                        $question->incNum();
                        $em->flush();
                        $res[$i++] = $line;
                    }
                    
                    $result = new Result();
                    $result->setLevel($level);
                    $result->setNum(self::NUM_TEST_QUESTIONS);
                    $result->setLevel($level);
                    $result->setRes($done);
                    $result->setMin($minset->getValue());
                    $result->setMax($maxset->getValue());
                    $em->persist($result);
                    $em->flush();
                    
                    $em->getConnection()->commit();
				} catch (\Exception $e){
                    $em->getConnection()->rollback();
					$errors[] = 'Ошибка базы данных.';
				}	
            }
            
            if (!$errors) {
                $status = 'success';
                $data = $res;
            } else {
                $status = 'error';
                $data = $errors;
            }
            echo json_encode(['status' => $status, 'data' => $data]);
            exit;
        }    
        return $this->notFound();
    }
    
    protected function getQuestions()
    {
        $em = $this->getEntityManager();
        $questions = $em->getRepository('\App\Models\Question')->findAll();
        $randdata = [];
        
        foreach ($questions as $item) {
            $randdata[$item->getId()] = (int)ceil(100/($item->getNum() + 1));
        }
        
        $res = [];
        for ($i = 0; $i < self::NUM_TEST_QUESTIONS; $i++) {
           $key = GenRand::getRandGen($randdata);
           $res[] = $key;
           unset($randdata[$key]);
        }
       
        return $res;
    }
}
