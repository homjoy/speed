<?php
namespace Atom\Package\Scripts;

use Atom\Package\Worker\SendMessage;
use Atom\Package\Approval\OrderAssets;
use Atom\Package\Account\UserInfo;
use Atom\Package\Core\Config;
use Frame\Speed\Lib\Api;
/**
 * 固定资产消息发送
 * @author minggeng@meilishuo.com
 * @data 2016-3-3 下午5:37:16
 */
class AssetsSendMessage{
	
	private static $instance = NULL;
    private $admin = array();

	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    private function _filter($data){
        $res = array();
        foreach($data as $d){
            $res[$d['key']] =  $d['value'];
        }
        return $res;
    }

    /**
     * 消息发送功能
     * @param string $params
     * @return boolean|number
     */
    public function sendMessage($params = array()){
        //流程单
        $order = OrderAssets::model()->getById($params['order_id']);

        //用户
        $user = UserInfo::model()->getById($order['user_id']);

        //部门
        $p = array(
            'depart_id'     => $user['depart_id'],
            'display_level' => 2,
        );
        $department = Api::atom('department/hacked_depart_info_list',$p);//部门信息

        //admin
        $output_manager = Config::model()->getChild('/executive/assets/admin');
        $this->admin = $this->_filter($output_manager);

        $sendParams = array(
            'user_id'       => $order['user_id'],
            'task_id'       => $order['task_id'],
            'depart_name'   => $department[$user['depart_id']]['depart_name'],
            'order_id'      => $order['order_id'],
            'order_type'    => '固定资产',
            'user_name'     => $user['name_cn'],
            'receive_user_mail'=> $user['mail'].'@meilishuo.com',
            'handle_user_id'=> $params['handle_user_id'],
        );
        switch($params['send_type']){
            case 1:     //提交
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
        $p = array(
            'task_id' => $params['task_id'],
            'speed_user_id' => 1,
        );
        $res_workflow = Api::workflow('task/get_task_info_by_id', $p);

        $direct_leader = $res_workflow['current_user_info'];

        if(!empty($direct_leader)){
            foreach($direct_leader as $d_l){
                //通知审批人
                $content = array(
                    'receive_name'  => $d_l['name'],
                    'username'      => "{$params['user_name']}（{$params['depart_name']}）",
                    'order_explains'=> '快来审批吧！',
                    'order_type'    => $params['order_type'],
                    'order_id'      => $params['order_id'],
                    'url'           => '/administration/fixedassets/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 223,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【固定资产申请提醒】申请人：{$params['user_name']}",
                    'mobile'        => '',
                    'mail'          => $d_l['mail'],
                );
                SendMessage::getInstance()->sendMail($mail_params);
                $mail_params['template_id'] = 123;
                SendMessage::getInstance()->sendIm($mail_params);
            }
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
            'order_explains'=> '赶紧帮忙准备吧！',
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/fixedassets/manage',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 219,
            'title'         => "【固定资产审批通过】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        foreach($this->admin as $admin_id => $admin_value){
            $a_admin = json_decode($admin_value,true);
            $mail_params['to_id']= $admin_id;
            $mail_params['mail'] = $a_admin['mail'];
            $mail_params['content']['receive_name'] = $a_admin['name'];
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
            'url'           => '/administration/fixedassets/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 221,
            'to_id'         => $params['user_id'],
            'title'         => "【固定资产申请驳回】申请人：{$params['user_name']}",
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
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'title'         => "【固定资产申请撤销】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        //admin
        foreach($this->admin as $admin_id => $admin_value){
            $a_admin = json_decode($admin_value,true);
            $mail_params['to_id']= $admin_id;
            $mail_params['mail'] = $a_admin['mail'];
            $mail_params['content']['receive_name'] = $a_admin['name'];
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
        $p = array(
            'task_id' => $params['task_id'],
            'speed_user_id' => 1,
        );
        $res_workflow = Api::workflow('task/get_task_info_by_id', $p);

        $direct_leader = $res_workflow['current_user_info'];
        if(!empty($direct_leader)){
            foreach($direct_leader as $d_l){
                //通知审批人
                $content = array(
                    'receive_name'  => $d_l['name'],
                    'username'      => "{$params['user_name']}（{$params['depart_name']}）",
                    'order_type'    => $params['order_type'],
                    'order_id'      => $params['order_id'],
                    'url'           => '/administration/fixedassets/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 222,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【固定资产申请提醒】申请人：{$params['user_name']}",
                    'mobile'        => '',
                    'mail'          => $d_l['mail'],
                );
                SendMessage::getInstance()->sendMail($mail_params);
                $mail_params['template_id'] = 122;
                SendMessage::getInstance()->sendIm($mail_params);
            }
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
            'receive_place' => '23层A区036',
            'order_type'    => $params['order_type'],
            'order_explains'=> "你的固定资产已准备好啦！领取地点：23层A区036",
            'order_id'      => $params['order_id'],
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 225,
            'to_id'         => $params['user_id'],
            'title'         => "【固定资产发放通知】",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 125;
        SendMessage::getInstance()->sendIm($mail_params);
    }
}
