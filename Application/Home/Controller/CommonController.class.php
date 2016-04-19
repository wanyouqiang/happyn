<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller {

	public function _initialize()
	{
		if (!session('?user')) {
			$this->error('尚未登录', U('Login/index'));
		}
	}

}