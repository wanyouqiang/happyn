<?php
namespace Home\Controller;

use Think\Controller;
use Common\Util\Excel;

class IndexController extends CommonController
{
	public function index()
	{
		$this->display();
	}

    public function outputExcel()
    {
    	$news = M('news')->field('id, title')
    		->where(['typeid' => 54])
    		->select();
    	$title = ['id', '标题'];
		// var_dump($news);
		Excel::export($news);
    }
}