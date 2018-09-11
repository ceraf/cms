<?php

namespace App\Controllers;

use App\Response;
use App\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if ($this->request->isPost()) {
            $login = $this->request->getPost('login');
            $pass = $this->request->getPost('password');
            if ($login && $pass) {
                $em = $this->getEntityManager();
                $user = $em->getRepository('\App\Models\User')
                        ->findOneBy(['username' => $login]);
                if ($user->getPassword() == md5($pass)) {
                    $this->setSession('user_id', $user->getId());
                }
            }
        }
        
        $this->redirect('/index/index');
    }
    
    public function logout()
    {
        $this->clearSession('user_id');
        $this->redirect('/index/index');
    }
}
