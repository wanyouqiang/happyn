<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller {
	public function index()
	{
		if (!IS_POST) {
			$this->display('login');
		} else {
			$username = trim(I('post.username'));
            $password = trim(I('post.password'));
            $code = trim(I('post.code'));

            //验证码非空校验
            if (empty($code) || $code === '') {
                $this->error('验证码不能为空');
            }

            if (!$this->checkVerify($code)) {
                $this->error('验证码错误');
            }

            // 用户名非空校验
            if (empty($username) || $username === '') {
                $this->error('用户名不能为空');
            }
            // 密码非空校验
            if (empty($password) || $password === '') {
                $this->error('密码不能为空');
            }

            $user = M('admin')->where(['username' => $username])->find();
            if (!$user) {
                $this->error('该用户不存在');
            }

            $password = md5(md5($password) . $user['encrypt']);

            if ($password != $user['password']) {
                $this->error('账户密码错误');
            }

            if (!$user) {
                $this->error('用户账号密码错误');
            }
            $session_user = [
                'username' => $user['username'],
                'login_time' => time()
            ];
            session('user', $session_user);
            $this->success('登录成功,正在跳转...','/Home/Index/index');
		}
	}


	public function nologin()
	{
		$this->show('尚未登录');
	}

	// 输出验证码
    public function getVerify()
    {
        $verify = new \Think\Verify();
        $verify->fontSize = 20;
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontttf = '6.ttf';
        $verify->entry();
    }

    // 验证码校验
    protected function checkVerify($code)
    {
        $verify = new \Think\Verify();
        return $verify->check($code);
    }
}
