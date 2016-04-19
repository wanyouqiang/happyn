<?php
namespace Home\Controller;
use Think\Controller;

class ToController extends Controller {
	public function view($id)
	{
		$catid = M('news')->field('catid')->find($id)['catid'];
		$url = 'http://web.happyn2.com/index.php?m=content&c=index&a=show&catid='.$catid.'&id='.$id;
		redirect($url);
	}
}