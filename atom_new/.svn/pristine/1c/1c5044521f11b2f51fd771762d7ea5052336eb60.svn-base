<?php
namespace Atom\Package\Scripts;

use Atom\Package\Worker\SendMessage;
use Atom\Package\Approval\OrderExpress;
use Atom\Package\Account\UserInfo;
use Atom\Package\Core\Config;
use Frame\Speed\Lib\Api;
/**
 * 快递消息发送
 * @author minggeng@meilishuo.com
 * @data 2015-11-24 下午5:37:16
 */
class ExpressSendMessage{
	
	private static $instance = NULL;
    private $admin = array();

	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    /**
     * 消息发送功能
     * @param string $params
     * @return boolean|number
     */
    public function sendMessage($params = array()){
        //快递单
        $order = OrderExpress::model()->getById($params['order_id']);

        //用户
        $user = UserInfo::model()->getById($order['user_id']);

        //部门
        $p = array(
            'depart_id'     => $user['depart_id'],
            'display_level' => 2,
        );
        $department = Api::atom('department/hacked_depart_info_list',$p);//部门信息

        $sendParams = array(
            'user_id'       => $order['user_id'],
            'task_id'       => $order['task_id'],
            'depart_name'   => $department[$user['depart_id']]['depart_name'],
            'order_id'      => $order['order_id'],
            'order_type'    => '快递',
            'user_name'     => $user['name_cn'],
            'receive_user_mail'=> $user['mail'].'@meilishuo.com',
            'handle_user_id'=> $params['handle_user_id'],
        );
        switch($params['send_type']){
            case 1:     //提交
                $this->_sendSubmitMessage($sendParams,true);
                break;
            /*case 2:     //审批通过
                $this->_sendSubmitMessage($sendParams,false);
                break;*/
            case 3:     //审批完成
                $this->_sendCompleteMessage($sendParams);
                break;
            case 4:     //驳回
                $this->_sendRejectMessage($sendParams);
                break;
            //case 5:     //撤销(本期不上)
            //    $this->_sendRevokeMessage($sendParams);
            //    break;
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
                    'url'           => '/administration/fastmail/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 223,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【快递申请提醒】申请人：{$params['user_name']}",
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
            'receive_name'  => $params['user_name'],
            'username'      => '你',
            'order_explains'=> '请将快递单交给前台寄出吧！',
            'order_type'    => $params['order_type'],
            'order_id'      => $params['order_id'],
            'url'           => '/administration/fastmail/manage',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 219,
            'to_id'         => $params['user_id'],
            'title'         => "【快递审批通过】申请人：{$params['user_name']}",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );

        $mail_params['template_id'] = 219;
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 119;
        SendMessage::getInstance()->sendIm($mail_params);
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
            'url'           => '/administration/fastmail/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 221,
            'to_id'         => $params['user_id'],
            'title'         => "【快递申请驳回】申请人：{$params['user_name']}",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 121;
        SendMessage::getInstance()->sendIm($mail_params);
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
                    'url'           => '/administration/fastmail/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 222,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【快递申请提醒】申请人：{$params['user_name']}",
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
     * 发放(申请人)
     * @param string $params
     */
    private function _sendGrantMessage($params){
        //通知申请人
        $content = array(
            'receive_name'  => $params['user_name'],
            'order_type'    => $params['order_type'],
            'order_explains'=> '你的快递已经寄出啦，注意跟踪物流信息哦！',
            'order_id'      => $params['order_id'],
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 225,
            'to_id'         => $params['user_id'],
            'title'         => "【快递寄出通知】",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 125;
        SendMessage::getInstance()->sendIm($mail_params);
    }
}