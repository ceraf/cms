<?php

namespace App\Controllers;

use App\Response;
use App\Controller;
use App\Models\Task;
use \stdClass;

use \Gumlet\ImageResize;


class IndexController extends Controller
{
    const MAX_ITEMS_PAGE = 3;
    const DEFAULT_SORT = 'id';
    const DEFAULT_SORT_TYPE = 'ASC';
    const SIZE_IMG_L = 320;
    const SIZE_IMG_H = 240;
	const MAX_USRNAME_LENGTH = 50;
	const MAX_EMAIL_LENGTH = 50;
    
	public function index()
	{
        $p = (int)$this->request->getParam('p');
        $sortby = $this->getSession('sort_by') ?? self::DEFAULT_SORT;
        $sorttype = $this->getSession('sort_type') ?? self::DEFAULT_SORT_TYPE;
            
        if ($this->request->getPost('sort_by') &&
                in_array($sortby, ['username', 'description', 'is_done', 'id'])) {
            $sorttype = ($sortby == $this->request->getPost('sort_by')) ? 'DESC' : 'ASC';       
            $sortby = $this->request->getPost('sort_by');
            $this->setSession('sort_by', $sortby);
            $this->setSession('sort_type', $sorttype);
        }
        
        $em = $this->getEntityManager();
        
        $entityname = '\App\Models\Task';

        $total = $em->createQueryBuilder()
                ->select('count(p.id)')
                ->from($entityname,'p')
                ->getQuery()
                ->getSingleScalarResult(); 
        
        
        if (!$total)
            $paginator = null;
        else {
            $paginator = new stdClass;
            $paginator->total = $total;
            $paginator->currpage = $p;
            $paginator->itemsonpage = self::MAX_ITEMS_PAGE;
            $paginator->numpages = ceil($paginator->total/$paginator->itemsonpage);          
        }
        
        $offset = $p*self::MAX_ITEMS_PAGE;
        $tasks = $em->getRepository('\App\Models\Task')
                ->getByPage($offset, self::MAX_ITEMS_PAGE, $sortby, $sorttype);

		return new Response('index/index', 
                [
                    'tasks' => $tasks,
                    'paginator' => $paginator,
                    'sortby' => $sortby,
                    'sorttype' => strtolower($sorttype),
					'msg' => $this->getFlashMessage()
                ]
        );
	}
    
    public function add()
    {
        $task = null;
        $errors = null;
        
        if ($this->request->isPost()) {
            $task = new Task();
          
            $task->setUsername(htmlentities ($this->request->getPost('username')));
            $task->setEmail(htmlentities ($this->request->getPost('email')));
            $task->setDescription(htmlentities ($this->request->getPost('description')));
            
            if (!$task->getUsername())
                $errors[] = 'Необходимо ввести имя пользователя.';
			elseif (strlen($task->getUsername()) > self::MAX_USRNAME_LENGTH)
				$errors[] = 'Имя пользователя длинне '.self::MAX_USRNAME_LENGTH.' символов.';
            if (!$task->getEmail())
                $errors[] = 'Необходимо ввести E-mail.';
            elseif (!filter_var($task->getEmail(), FILTER_VALIDATE_EMAIL))
                $errors[] = 'E-mail адрес '.$task->getEmail().' указан не верно.';
			elseif (strlen($task->getEmail()) > self::MAX_USRNAME_LENGTH)
				$errors[] = 'E-mail длинне '.self::MAX_EMAIL_LENGTH.' символов.';
            if (!$task->getDescription())
                $errors[] = 'Необходимо ввести текст задачи.';
            
            if (!$errors) {
                $file = $this->request->getFile('preview');
                if ($file && $file['name']) {
					$fileName = $this->saveImage($file);			
					$task->setPreview($fileName);
                }
				try {
					$em = $this->getEntityManager();
					$em->persist($task);
					$em->flush();
					$this->setFlashMessage('success', 'Задача успешно добавлена.');
					$this->redirect('/index/index');
				} catch (\Exception $e){
					$errors[] = 'Ошибка базы данных.';
				}		
            }
        }
		return new Response('index/edit', ['task' => $task, 'errors' => $errors, 'mode' => 'add']);        
    }
    
    public function edit()
    {
		$errors = null;
        $id = (int)$this->request->getParam('id');
        $em = $this->getEntityManager();
        $task = $em->getRepository('\App\Models\Task')
                    ->find($id);
        if (!$task || !$this->isAdmin()) {
			$this->redirect('/index/index');            
        }
        
        if ($this->request->isPost()) {
			$task->setDescription(htmlentities ($this->request->getPost('description')));
			if (!$task->getDescription())
                $errors[] = 'Необходимо ввести текст задачи.';
			$task->setIsDone(($this->request->getPost('is_done') == '1') ? '1' : '0');
			if (!$errors) {
				try {
					$em = $this->getEntityManager();
					$em->persist($task);
					$em->flush();		
					$this->setFlashMessage('success', 'Задача сохранена.');
					$this->redirect('/index/index');
				} catch (\Exception $e){
					$errors[] = 'Ошибка базы данных.';
				}		
			}
		}
		
		return new Response('index/edit', ['task' => $task, 'mode' => 'edit', 'errors' => $errors]);           
    }
    
    protected function saveImage($file)
    {
		$path_parts = pathinfo($file['name']);
		$fileName = md5(uniqid()).'.'.$path_parts['extension'];
		$image = new ImageResize($file['tmp_name']);
		$image->resizeToBestFit(self::SIZE_IMG_L, self::SIZE_IMG_H);
		$image->save(PROJECT_DIR .'/public/images/'.$fileName);
        return $fileName;
    }
}
