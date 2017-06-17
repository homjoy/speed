<?php
namespace Atom\Package\Scripts;

use Atom\Package\Worker\SendMessage;
use Atom\Package\Approval\OrderOfficeSupply;
use Atom\Package\Account\UserInfo;
use Atom\Package\Core\Config;
use Frame\Speed\Lib\Api;
/**
 * 办公用品消息发送
 * @author minggeng@meilishuo.com
 * @data 2016-01-13 下午5:37:16
 */
class OfficeSendMessage{
	
	private static $instance = NULL;
    private $admin = array();

	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    private function _getAdmin($order){
        //流程结束状态
        $complete_type = array(0,4,5);

        $admin = array();
        switch($order['order_type']){
            case 1:     //日常用品
                $output_manager = Config::model()->getValue("/executive/admin/{$order['post_place']}");
                $admin = json_decode($output_manager['value'],true);
                break;
            case 2:     //通讯用品
                $p = array(
                    'task_id' => $order['task_id'],
                    'speed_user_id' => 1,
                );
                $res_workflow = Api::workflow('task/get_task_info_by_id', $p);

                if(in_array($res_workflow['status'],$complete_type)){
                    $output_manager = Config::model()->getValue("/executive/office_supply/managers/2");
                    $admin = json_decode($output_manager['value'],true);
                }else{
                    $admin = $res_workflow['current_user_info'];
                    $admin[0]['id'] = $admin[0]['user_id'];
                }
                break;
            case 3:     //电脑配件
                $output_manager = Config::model()->getValue("/executive/office_supply/managers/3");
                $admin = json_decode($output_manager['value'],true);
                break;
            case 4:     //数据线
                $output_manager = Config::model()->getValue("/executive/office_supply/managers/4");
                $admin = json_decode($output_manager['value'],true);
                break;
            default:
                break;
        }

        return $admin;
    }

    /**
     * 消息发送功能
     * @param string $params
     * @return boolean|number
     */
    public function sendMessage($params = array()){
        //办公用品单
        $order = OrderOfficeSupply::model()->getById($params['order_id']);

        //用户
        $user = UserInfo::model()->getById($order['user_id']);

        //部门
        $p = array(
            'depart_id'     => $user['depart_id'],
            'display_level' => 2,
        );
        $department = Api::atom('department/hacked_depart_info_list',$p);//部门信息

        //admin
        $this->admin = $this->_getAdmin($order);

        $sendParams = array(
            'user_id'       => $order['user_id'],
            'task_id'       => $order['task_id'],
            'depart_name'   => $department[$user['depart_id']]['depart_name'],
            'order_id'      => $order['order_id'],
            'office_type'   => $order['order_type'],//办公用品分类
            'order_type'    => '办公用品',
            'user_name'     => $user['name_cn'],
            'receive_user_mail'=> $user['mail'].'@meilishuo.com',
            'handle_user_id'=> $params['handle_user_id'],
        );
        switch($params['send_type']){
            case 1:     //提交
                $this->_sendSubmitMessage($sendParams);
                break;
            case 2:     //审批通过
                $this->_sendSubmitMessage($sendParams);
                break;
            case 3:     //审批完成
                $this->_sendCompleteMessage($sendParams);
                break;
            case 4:     //驳回
                $this->_sendRejectMessage($sendParams);
                break;
            case 5:     //撤销
                $this->_sendRevokeMessage($sendParams);
                break;
            case 6:     //催单
                $this->_sendReminderMessage($sendParams);
                break;
            case 7:     //发放
                $this->_sendGrantMessage($sendParams);
                break;
            default:
                break;
        }
    }

    /**
     * 提交(审批人)
     * @param string $params
     * @param string $is_submit true
     */
    private function _sendSubmitMessage($params){

        //通知审批人
        $content = array(
            'username'      => "{$params['user_name']}（{$params['depart_name']}）",
            'order_explains'=> '快来审批吧！',
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/approval',
        );

        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 223,
            'title'         => "【办公用品申请提醒】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        foreach($this->admin as $admin_id => $admin_value){
            $mail_params['to_id']= $admin_value['id'];
            $mail_params['mail'] = $admin_value['mail'];
            $mail_params['content']['receive_name'] = $admin_value['name'];
            $mail_params['template_id'] = 223;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 123;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }

    /**
     * 完成(admin)
     * @param string $params
     */
    private function _sendCompleteMessage($params){
        //通知admin
        $content = array(
            'username'      => "{$params['user_name']}（{$params['depart_name']}）",
            'order_explains'=> '请准备发放吧！',
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/manage',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 219,
            'title'         => "【办公用品审批通过】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        foreach($this->admin as $admin_id => $admin_value){
            $mail_params['to_id']= $admin_value['id'];
            $mail_params['mail'] = $admin_value['mail'];
            $mail_params['content']['receive_name'] = $admin_value['name'];
            $mail_params['template_id'] = 219;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 119;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }

    /**
     * 驳回(申请人)
     * @param string $params
     */
    private function _sendRejectMessage($params){
        //通知申请人
        //用户
        $handle_user = UserInfo::model()->getById($params['handle_user_id']);

        $content = array(
            'receive_name'  => $params['user_name'],
            'username'      => '你',
            'approve_name'  => $handle_user['name_cn'],
            'order_explains'=> '',
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 221,
            'to_id'         => $params['user_id'],
            'title'         => "【办公用品申请驳回】申请人：{$params['user_name']}",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 121;
        SendMessage::getInstance()->sendIm($mail_params);
    }

    /**
     * 撤销(admin)
     * @param string $params
     */
    private function _sendRevokeMessage($params){
        $content = array(
            'receive_name'  => $params['user_name'],
            'username'      => "{$params['user_name']}（{$params['depart_name']}）",
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/approval',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'title'         => "【办公用品申请撤销】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        foreach($this->admin as $admin_id => $admin_value){
            $mail_params['to_id']= $admin_value['id'];
            $mail_params['mail'] = $admin_value['mail'];
            $mail_params['content']['receive_name'] = $admin_value['name'];
            $mail_params['template_id'] = 220;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 120;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }

    /**
     * 催审(审批人)
     * @param string $params
     */
    private function _sendReminderMessage($params){
        //通知审批人
        $content = array(
            'username'      => "{$params['user_name']}（{$params['depart_name']}）",
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/approval',
        );

        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 222,
            'title'         => "【办公用品申请提醒】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        foreach($this->admin as $admin_id => $admin_value){
            $mail_params['to_id']= $admin_value['id'];
            $mail_params['mail'] = $admin_value['mail'];
            $mail_params['content']['receive_name'] = $admin_value['name'];
            $mail_params['template_id'] = 222;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 122;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }

    /**
     * 发放(审批人)
     * @param string $params
     */
    private function _sendGrantMessage($params){
        //通知申请人
        $content = array(
            'receive_name'  => $params['user_name'],
            'receive_place' => $this->admin[0]['position'],//'26层前台',
            'order_type'    => $params['order_type'],
            'order_explains'=> "你的办公用品已准备好啦！",
            'order_id'      => $params['order_id'],
            'url'           => '/administration/officesupply/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 225,
            'to_id'         => $params['user_id'],
            'title'         => "【办公用品发放通知】",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 125;
        SendMessage::getInstance()->sendIm($mail_params);

        $output_manager = Config::model()->getValue("/executive/office_supply/managers/pc_manager");
        $admin = json_decode($output_manager['value'],true);
        $admin = array_pop($admin);
        //通知管理员李丹
        if($params['office_type'] == 3){//电脑配件
            $content = array(
                'receive_name'  => $admin['name'],
                'receive_place' => $admin['position'],//'26层前台',
                'order_type'    => $params['order_type'],
                'order_explains'=> "{$params['user_name']}的办公用品已准备好啦！",
                'order_id'      => $params['order_id'],
                'url'           => '/administration/officesupply/my',
            );
            $mail_params = array(
                'token_type'    => 'process',
                'content'       => $content,
                'template_id'   => 225,
                'to_id'         => $admin['id'],
                'title'         => "【办公用品发放通知】",
                'mobile'        => '',
                'mail'          => $admin['mail'],
            );
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 125;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }
}
