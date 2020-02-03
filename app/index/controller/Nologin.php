<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Cookie;

class Nologin extends BaseController
{	
    public function login()
    {
       return View::fetch();
    }
    public function admin()
    {
        $username = Cookie::get('username');
        if(empty($_POST)&&empty($username)){
          return redirect('/index/login.html');
        }
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $pwd = md5('12345678');
        if(empty($username)||empty($password)||trim($username)!='admin'||md5(trim($password))!=$pwd){
          return redirect('/index/login.html');          
        }
        Cookie::set('username', 'admin', 60*60*24);
        return redirect('/index/mylist.html');
    }

}