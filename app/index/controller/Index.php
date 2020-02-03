<?php
namespace app\index\controller;

use think\facade\Cookie;
use think\facade\View;
use app\index\service\Nmysql;
use think\Config; 

/*
<option value="1">句子(juzi)</option>
<option value="2">标题(bt)</option>
<option value="3">图片(pic)</option>
<option value="4">小图(img)</option>
<option value="5">权重标题(wzmz)</option>
<option value="6">栏目名称(lanmu)</option>
<option value="7">关键词(keyword)</option>
<option value="8">吸引标题(zhon)</option>
<option value="9">关键词2(hou)</option>
<option value="10">外链(wailian)</option>
<option value="11">模板(moban)</option>
<option value="12">juzi2</option>
<option value="13">ditu</option>
 */
class Index extends Common
{
    const TableMap = ['1' => 'juzi', '2' => 'bt', '3' => 'pic', '4' => 'img', '5' => 'wzmz', '6' => 'lanmu', '7' => 'keyword', '8' => 'zhon', '9' => 'hou', '10' => 'wailian', '11' => 'moban', '12' => 'juzi2', '13' => 'bt_keyword', '14' => 'ditu'];
    public function index()
    {
        return redirect('/index/subNews.html');
    }
    public function checkLogin()
    {
        $username = Cookie::get('username', '');
        return $username;
    }
    public function rand($len)
    {
        $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
        $string=time();
        $len = (int)$len;
        for($len=$len;$len>=1;$len--)
        {
            $position=rand()%strlen($chars);
            $position2=rand()%strlen($string);
            $string=substr_replace($string,substr($chars,$position,1),$position2,0);
        }
        return md5($string.microtime());
    }    
    public function doFile(){
        $file = request()->file('file_fr');
        $dirSep = '/';//DIRECTORY_SEPARATOR
        if ($file) {
            $name = $_FILES['file_fr']['name'];
            $extends = strrchr($name,'.');

            if(!in_array(trim($extends), ['.html','.htm','.txt'])){
                echo json_encode(['error'=>'不正确的文件类型']);
            }
            // 移动到框架应用根目录/public/uploads/ 目录下
            $rand = $this->rand(20).$extends;
            $pName = $_SERVER['DOCUMENT_ROOT']. $dirSep . 'upload'.$dirSep.$rand;
            $info = $file->move($_SERVER['DOCUMENT_ROOT']. $dirSep . 'upload',$rand);
            //var_dump($pName);var_dump($info);exit;
            if($info&&file_exists($pName)){
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo json_encode(['uploaded' => $pName]);
            }else{
                echo json_encode(['error'=>'上传失败']);
            }  
        } else {
            echo json_encode(['error'=>'No files found for upload.']);
        }        
    }
    public function subNews()
    {
        if (!$this->checkLogin()) {
            return redirect('/index/login.html');
        }
        if (!empty($_POST)) {
            $paths = (string) trim($_POST['paths']);
            if (empty($paths)) {
                return ['status' => 1];
            }
            //$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $path;
            $paths = explode(',', trim($paths,','));
            $data   = [];
            $type = $_POST['type'];
            $_data  = ['type' => $type];
            if ($type == '11') {
                $_data['moban'] = (int) $_POST['moban'];
            }
            $_data = json_encode($_data);
            foreach ($paths as $value) {
                if (!file_exists($value)) {
                    return ['status' => 1];
                }
                $data[] = ['path'=>$value,'data'=>$_data,'type'=>(int)$type];
            }
            $status = 0;

            $status       = \think\facade\Db::name('add_queue')->insertAll($data);
            if ($status) {
                return ['status' => 0];
            } else {
                return ['status' => 1];
            }
        }

        return View::fetch();
    }
    public function myList()
    {
        if (!$this->checkLogin()) {
            return redirect('/index/login.html');
        }
        $type   = $title   = '';
        $data[] = ['id', '>', 0];
        $list   = \think\facade\Db::connect('db2')->name('map')->where($data)->order('id', 'desc')->paginate(['list_rows' => 50]);

        // 获取分页显示
        //$page = $list->render();
        View::assign('list', $list);
        return View::fetch();
    }
    public function hostf()
    {
        return View::fetch();
    }
    public function addHost()
    {
        $dbInfo = config('database.connections.db2');
        $info = [
            'host'=>$dbInfo['hostname'],
            'port'=>$dbInfo['hostport'],
            'user'=>$dbInfo['username'],
            'passwd'=>$dbInfo['password'],
            'dbname'=>$dbInfo['database']
        ];
        $nmysql = new Nmysql($info);
        $url = trim($_GET['url']);
        $res = $nmysql->field(array('id'))
            ->where(array('url'=>"'".$url."'"))
            ->limit(1)
            ->select('host_map');
        if(!empty($res)||!empty(current($res))){
            return ['status'=>0];
        }
        $res = $this->add($nmysql,$url);
        if($res){
            return ['status'=>0];
        }else{
            return ['status'=>1];
        }

    }   

    public function add($mysql,$url){
        $mysql->startTrans();
        try{
            $tableId = $mysql->insert("host_map",['url'=>$url],1);var_dump($tableId);
            if(empty($tableId)){
                $mysql->rollback();
                return false;
            }
            $oldTable = "host_visiter_samp";
            $newTable = "host_visiter{$tableId}_1";
            $sql = "create table {$newTable} like {$oldTable}";
            $res=$mysql->doSql($sql);
            $sql = "select * from information_schema.tables where table_name ='".$newTable."'";
            $res=$mysql->doSql($sql);
            if(empty($res)){
                $mysql->where(['id'=>$tableId])->delete("host_map");
                $mysql->rollback();
                return false;
            }
            $tableId = $mysql->insert("host_valid_table",['name'=>"visiter{$tableId}"],1);
            if(empty($tableId)){
                $this->mysql->rollback();
                return false;
            }
            $mysql->commit();        
            return true;
        }catch(\Exception $e){
            $mysql->rollback();
            return false;
        }       
    }     
    /*
    public function addHost()
    {
        if (!$this->checkLogin()) {
            return redirect('/index/login.html');
        }
        $url = trim($_POST['url']);
        if (empty($url)) {
            return ['status' => 1];
        }
        $db   = \think\facade\Db::connect('db2');
        $data = $db->query("select id from host_map where url='{$url}'");
        if (!empty($data)) {
            return ['status' => 0];
        }
        $id = $db->table('host_map')->insertGetId(['url' => $url]);
        $db->query('begin');
        try {
            $id = $db->table('host_map')->insertGetId(['url' => $url]);
            echo $id;
            $db->query('rollback');exit('3');
            if($id==false){
                $db->rollback();
                return ['status' => 1];
            }
            $newTable = "host_visiter{$id}_1";
            $sql = "create table {$newTable} like host_visiter_samp";
            $data = $db->query($sql);
            echo $data;$db->rollback();exit('3');
            $sql = "select * from information_schema.tables where table_name ='".$newTable."'";
            $data = $db->query($sql);var_dump($data);$db->rollback();return ['status'=>1];
            if (empty($data)) {
                $db->rollback();
                return ['status' => 1];
            }
            $id = $db->table('host_valid_table')->insertGetId(['name' => "visiter{$id}",'num'=>1]);
            $id = false;
            if($id==false){
                $db->rollback();
                return ['status' => 3];
            }
            // 提交事务
           $db->commit();
           return ['status' => 0];
        } catch (\Exception $e) {
            // 回滚事务
            $db->query('rollback');exit('3');
        }
        return ['status' => 1];
    }
    */
/*    public function addHost()
    {
        if (!$this->checkLogin()) {
            return redirect('/index/login.html');
        }
        $url = trim($_POST['url']);
        if (empty($url)) {
            return ['status' => 1];
        }
        $db   = \think\facade\Db::connect('db2');
        $data = $db->query("select id from host_map where url='{$url}'");
        if (!empty($data)) {
            return ['status' => 0];
        }
        $id = $db->table('host_map')->insertGetId(['url' => $url]);
        if(!$id){
            return ['status'=>1];
        }
        $id = $db->table('host_map')->insertGetId(['url' => $url]);

        return ['status' => 1];
    }*/
    public function dellogin()
    {
        if (!$this->checkLogin()) {
            return redirect('/index/login.html');
        }
        Cookie::delete('username');
        return redirect('/index/login.html');
    }
}
