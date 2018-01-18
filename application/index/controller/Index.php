<?php
namespace app\index\controller;

use think\Controller;

use think\Cache;

session_start();
class Index  extends Controller
{
    //首页
    public  function index(){
        phpinfo();exit;
        print_r($_COOKIE);
        $this->assign('username',session('username'));
        return $this->fetch('index');
        
    }
    //验证码
    public function yanzhengma(){
        return $this->fetch('yanzhengma');
    }
    //处理验证码
    public function do_yanzhengma(){
        $yanzheng=$_GET['yanzheng'];
        if (captcha_check($yanzheng)) {
            $this->success('验证码输入成功');

        } else {
            $tishi=$this->error('验证码输入错误,请重新输入');
            echo "<script>alert('$tishi');window.location.href='Index/yanzhengma'</script>"; 
        }
        
        
    }
    //分页
    public function fenye(){
        $db=db('student');
        $list=$db->paginate(config('paginate.list_rows'));
        $data=$db->select();
        // 获取分页显示
        // $page = $list
        // echo "<pre>";
        // print_r($data);exit;
        // 模板变量赋值
        $this->assign('list', $data);
        $this->assign('page', $list->render());
        // 渲染模板输出
        return $this->fetch('fenye');
    }
    // 注册页面
    public function zhuce(){
    	return $this->fetch('zhuce');
    }
    //处理注册页面
    public function do_zhuce(){
    	$data=input('post.');
    	$username=$_POST['username'];
    	$data['password']=md5($_POST['password']);
    	$data['repassword']=md5($_POST['repassword']);
    	$db=db('student');
    	$find1=$db->where('username="'.$username.'"')->find();
    	if($find1){
    		$this->error('用户名已存在');
    		
    	}else{
    		if($data['password']==$data['repassword']){
    			$info=$db->insert($data);
			    	if($info){
			    		$this->error('插入成功');
			    	}else{
			    		$this->error('插入失败');
			    	}
    		}
    		else{
    			$this->error('两次输入密码不一致');
    		}
    	}
    }
    //登录页面
    public function login(){

        return $this->fetch('login');
    }
    //处理登陆
    public function do_login(){
        $jizhu=input('post.remember');
        $username=input('post.username');
        $password=md5(input('post.password'));
        $table=db('student');
        $info=$table->where('username="'.$username.'"')->find();
        //echo "<pre>";
        //print_r($info);
        if($info)
        {
            if($info['password']==$password)
            {
                session('username',$username);
                if($jizhu){
                    cookie('username',$username,3600);
                }
                $this->redirect('Index/index');
            }
            else{
                $this->error('密码错误,登录失败');
            }
        }
        else{
            $this->error('登录失败，用户名不存在');
        }
    }
    //退出登录
    public function des(){
    unset($_SESSION['username']);//清除session值
    $sess=session_destroy();
    if ($sess) {
        $this->redirect('Index/login');

    } else {
        echo "退出失败";
    }
    
    }
}




?>



