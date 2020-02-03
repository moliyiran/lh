<?php
namespace app\index\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Cookie;
use think\exception\HttpResponseException;
use think\Response;

class Common extends BaseController
{

/*	public function _initialize(){
        $username = Cookie::get('username','');
        if(empty($username)||$username==null){
          return redirect('/admin/login.html');
        }
　　}*/
/*    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V6<br/><span style="font-size:30px">13载初心不改 - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }*/
     /**
       * 操作成功跳转的快捷方法
       * @access protected
       * @param mixed $msg 提示信息
       * @param string $url 跳转的URL地址
       * @param mixed $data 返回的数据
       * @param integer $wait 跳转等待时间
       * @param array $header 发送的Header信息
       * @return void
       */
      protected function success($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
      {
           if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
           $url = $_SERVER["HTTP_REFERER"];
           } elseif ($url) {
           $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app->route->buildUrl($url);
           }
           $result = [
           'code' => 1,
           'msg' => $msg,
           'data' => $data,
           'url' => $url,
           'wait' => $wait,
           ];
           $type = $this->getResponseType();
           // 把跳转模板的渲染下沉，这样在 response_send 行为里通过getData()获得的数据是一致性的格式
           if ('html' == strtolower($type)) {
           $type = 'view';
           }
           $response = Response::create($result, $type)->header($header)->options(['jump_template' => app()->config->get('app.dispatch_success_tmpl')]);
           throw new HttpResponseException($response);
      }
      /**
       * 操作错误跳转的快捷方法
       * @access protected
       * @param mixed $msg 提示信息
       * @param string $url 跳转的URL地址
       * @param mixed $data 返回的数据
       * @param integer $wait 跳转等待时间
       * @param array $header 发送的Header信息
       * @return void
       */
      protected function error($msg = '', string $url = null, $data = '', int $wait = 3, array $header = [])
      {
           if (is_null($url)) {
           $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
           } elseif ($url) {
           $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app->route->buildUrl($url);
           }
           $result = [
           'code' => 0,
           'msg' => $msg,
           'data' => $data,
           'url' => $url,
           'wait' => $wait,
           ];
           $type = $this->getResponseType();
           if ('html' == strtolower($type)) {
           $type = 'view';
           }
           $response = Response::create($result, $type)->header($header)->options(['jump_template' => app()->config->get('app.dispatch_success_tmpl')]);
           throw new HttpResponseException($response);
      }    
}
