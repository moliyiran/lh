<?php
namespace app\index\service;

class Dealobj extends Common
{
    public $txt = [];
    public function setTxt($content){
        $this->txt['content'] = trim($content);
        $this->txt['key'] = trim(md5($this->txt['content']));
        return $this;
    }
    public function getInsertValidTable($tableName){
        $info = \think\facade\Db::name('valid_table')->where(['name'=>$tableName])->find();
        return ['tableName'=>"{$tableName}_".$info['num'],'num'=>$info['num']];
    }

    public function checkTxt($tableName){
        $info = $this->getInsertValidTable($tableName);
        $num = (int)$info['num']; 
        $k = $this->txt['key'];
        $data = [];
        for($i=1;$i<=$num;$i++){
          $curTable = "{$tableName}_{$i}";
          $data = \think\facade\Db::name($curTable)->where(['mkey'=>$k])->column('id')->findOrEmpty();
          if(!empty($info)){
              break;
          }
        }
        return $data;


        return ['tableName'=>"{$tableName}_".$info['num'],'num'=>$info['num']];
    }
    public function dt($tableName,$path){
        $obj = $this->setTxt(file_get_contents($path));
        $data = $obj->checkTxt($tableName);
        if(empty($data)){
                      
        }
        $list = \think\facade\Db::name('news')->where($data)->order('id', 'desc')->paginate(['list_rows'=>3,'query'=>$query]);

    } 
    public function lineJuze($path){

    }  
}  