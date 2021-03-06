<?php
namespace app\user\controller;

use app\admin\model\UserModel;
use app\UserBaseController;
use think\Db;

class Index extends UserBaseController
{
    /**
     * 用户首页
     * @return mixed
     */
    public function index(){
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);
        return $this->fetch();
    }
    public function center(){
        //获取登录用户信息
        $user = getUser();
        $user['major'] = Db::name('major')->where('id',$user['major_id'])->value('name');
        $user['class'] = Db::name('class')->where('id',$user['class_id'])->value('name');
        $this->assign($user);
        return $this->fetch();
    }
    /**
     * 修改密码页面
     */
    public function changePassword(){
        //获取登录用户信息
        $user = getUser();
        $this->assign($user);
        return $this->fetch();
    }
    /**
     * 修改密码提交
     */
    public function changePasswordPost(){
        if(!$this->request->isPost()){
            $this->error('请求失败');
        }
        $post = $this->request->post();
        $username = $post['username'];
        $oldPassword = $post['oldPassword'];
        $newPassword = $post['newPassword'];
        if($username=='' || $newPassword=='' || $oldPassword==''){
            $this->error('用户名,旧密码和新密码不能为空');
        }
        $model = new UserModel();
        $result = $model->changePassword($username,$oldPassword,$newPassword);//登录验证
        if($result['code']==1){
            addLog('修改密码',getUserId());//记录日志
            $this->success($result['msg']);
        }else{
            $this->error($result['msg']);
        }
    }
    /**
     * 检测是否是选课时间
     */
    public function checkSelectStatus(){
        if(!$this->request->isGet()){
            $this->error('请求失败');
        }
        $startStopTime = Db::name('option')->where('name','select_start_and_stop_time')->value('value');
        $startStopTime = json_decode($startStopTime,1);
        $now = time();
        if($now<$startStopTime['start_time'] || $now>$startStopTime['stop_time']){
            $this->error('现在不是选课时间');
        }else{
            $this->success();
        }
    }
    /**
     * 检测是否是开课时间
     */
    public function checkCreateStatus(){
        if(!$this->request->isGet()){
            $this->error('请求失败');
        }
        $startStopTime = Db::name('option')->where('name','create_start_and_stop_time')->value('value');
        $startStopTime = json_decode($startStopTime,1);
        $now = time();
        if($now<$startStopTime['start_time'] || $now>$startStopTime['stop_time']){
            $this->error('现在不是开设课程的时间');
        }else{
            $this->success();
        }
    }
}
