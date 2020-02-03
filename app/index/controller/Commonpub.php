<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\View;

class Commonpub extends Common
{

    public function hkheader()
    {
       return View::fetch();
    }

    public function sixfastHeader()
    {
       return View::fetch();
    }

    public function head()
    {
       return View::fetch();
    }

    public function fooder()
    {
       return View::fetch();
    }
}
