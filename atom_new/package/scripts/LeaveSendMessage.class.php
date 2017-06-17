<?php
namespace Atom\Package\Scripts;

use Atom\Package\Worker\SendMessage;
use Atom\Package\Approval\OrderLeave;
use Atom\Package\Account\UserExtendedRelation;
use Atom\Package\Account\UserInfo;
use Atom\Package\Core\Config;
use Frame\Speed\Lib\Api;
/**
 * 请假消息发送
 * @author minggeng@meilishuo.com
 * @data 2015-11-4 下午5:37:16
 */
class LeaveSendMessage{
	
	private static $instance = NULL;
    private $hrbp = array();
    private $hrer = array();

	private function __construct() {}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    private function _leaveDate($params){
        $start_date_time = strtotime($params['start_date']);
        $end_date_time = strtotime($params['end_date']);
        $start_date = date('Y年n月d日',$start_date_time);
        $end_date = date('Y年n月d日',$end_date_time);

        if($params['start_half'] == 'AM'){
            $start_half = '上午';
        }elseif($params['start_half'] == 'PM'){
            $start_half = '下午';
        }else{
            $start_half = '上午';
        }

        if($params['end_half'] == 'AM'){
            $end_half = '上午';
        }elseif($params['end_half'] == 'PM'){
            $end_half = '下午';
        }else{
            if($start_date == $end_date){
                if(empty($params['start_half'])){
                    $end_half = '下午';
                }else{
                    $end_half = $start_half;
                }
            }else{
                $end_half = '下午';
            }
        }

        $res_data = $start_date.$start_half.'--'.$end_date.$end_half;
        return $res_data;
    }

    /**
     * 获取请假类型名称
     * @param int $type
     * @return array
     */
    private function _getAbsenceTypeName($type){
        $array = array(
            1 => '事假',
            2 => '年假',
            3 => '病假',
            4 => '带薪病假',
            5 => '婚假',
            6 => '丧假',
            7 => '产假',
            8 => '陪产假',
            9 => '产检假',
            10 => '流产假',
        );
        return $array[$type];
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
        //请假单
        $order = OrderLeave::model()->getDataById($params['order_id']);

        //用户
        $user = UserInfo::model()->getById($order['user_id']);

        //部门
        $p = array(
            'depart_id'     => $user['depart_id'],
            'display_level' => 2,
        );
        $department = Api::atom('department/hacked_depart_info_list',$p);//部门信息

        //hrbp
        $this->_getHrbp($order['user_id']);

        //hrer
        $hrer_res = Config::model()->getChild('/hr/leave/hrer');
        $this->hrer = $this->_filter($hrer_res);

        $sendParams = array(
            'user_id'       => $order['user_id'],
            'task_id'       => $order['task_id'],
            'depart_name'   => $department[$user['depart_id']]['depart_name'],
            'leave_memo'    => $order['memo'],
            'order_id'      => $order['order_id'],
            'leave_days'    => $order['length'].'天',
            'leave_date'    => $this->_leaveDate($order),
            'order_type'    => $this->_getAbsenceTypeName($order['absence_type']),
            'user_name'     => $user['name_cn'],
            'receive_user_mail'=> $user['mail'].'@meilishuo.com',
            'handle_user_id'=> $params['handle_user_id'],
        );
        switch($params['send_type']){
            case 1:     //提交
                $this->_sendSubmitMessage($sendParams,true);
                break;
            case 2:     //审批通过
                $this->_sendSubmitMessage($sendParams,false);
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
            default:
                break;
        }
    }

    /**
     * 获取hrbp
     * @param int $user_id
     */
    private function _getHrbp($user_id){
        $queryParams = array(
            'user_id' => $user_id,
            'type'    => 2,
        );
        $data = UserExtendedRelation::model()->getList($queryParams);
        $data = array_pop($data);
        $user_info = UserInfo::model()->getById($data['relation_user_id']);
        $this->hrbp = array(
            'user_id'   => $data['relation_user_id'],
            'mail'      => $user_info['mail'].'@meilishuo.com',
            'user_name' => $user_info['name_cn'],
        );
    }

    /**
     * 提交(审批人&hrbp)
     * @param string $params
     * @param string $is_submit true：需要发送hrbp
     */
    private function _sendSubmitMessage($params,$is_submit){
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
                    'leave_date'    => $params['leave_date'],
                    'leave_days'    => $params['leave_days'],
                    'leave_memo'    => $params['leave_memo'],
                    'order_id'      => $params['order_id'],
                    'url'           => '/hr/leave/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 223,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【请假申请提醒】申请人：{$params['user_name']}",
                    'mobile'        => '',
                    'mail'          => $d_l['mail'],
                );
                SendMessage::getInstance()->sendMail($mail_params);
                $mail_params['template_id'] = 123;
                SendMessage::getInstance()->sendIm($mail_params);
            }
        }

        //通知hrbp
        if($is_submit && $this->hrbp){
            $mail_params['content']['order_explains'] = '';
            $mail_params['content']['receive_name'] = $this->hrbp['user_name'];
            $mail_params['to_id']= $this->hrbp['user_id'];
            $mail_params['mail'] = $this->hrbp['mail'];
            $mail_params['template_id'] = 223;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 123;
            SendMessage::getInstance()->sendIm($mail_params);
        }
    }

    /**
     * 完成(申请人&hrer&hrbp)
     * @param string $params
     */
    private function _sendCompleteMessage($params){
        //通知申请人
        $content = array(
            'receive_name'  => $params['user_name'],
            'username'      => '你',
            'order_explains'=> "记得提前做好休假期间工作交接哦！若您预支的年假，离职时将按照年假生成规则进行计算，若超出应休年假天数将进行扣除！",
            'order_type'    => $params['order_type'],
            'leave_date'    => $params['leave_date'],
            'leave_days'    => $params['leave_days'],
            'leave_memo'    => $params['leave_memo'],
            'order_id'      => $params['order_id'],
            'url'           => '/hr/leave/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 219,
            'to_id'         => $params['user_id'],
            'title'         => "【请假审批通过】申请人：{$params['user_name']}",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 119;
        SendMessage::getInstance()->sendIm($mail_params);

        //hrer
        foreach($this->hrer as $hrer_id => $hrer_value){
            $hr_er = json_decode($hrer_value,true);
            $mail_params['to_id']= $hrer_id;
            $mail_params['mail'] = $hr_er['mail'];
            $mail_params['content']['receive_name'] = $hr_er['name'];
            $mail_params['content']['username'] = "{$params['user_name']}（{$params['depart_name']}）";
            $mail_params['content']['order_explains'] = '';
            $mail_params['template_id'] = 219;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 119;
            SendMessage::getInstance()->sendIm($mail_params);
        }

        //hrbp
        $mail_params['to_id']= $this->hrbp['user_id'];
        $mail_params['mail'] = $this->hrbp['mail'];
        $mail_params['content']['receive_name'] = $this->hrbp['user_name'];
        $mail_params['template_id'] = 219;
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 119;
        SendMessage::getInstance()->sendIm($mail_params);
    }

    /**
     * 驳回(申请人&hrer&hrbp)
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
            'order_explains'=> '你留下对我们来说很重要，再坚持坚持吧！',
            'order_type'    => $params['order_type'],
            'leave_date'    => $params['leave_date'],
            'leave_days'    => $params['leave_days'],
            'leave_memo'    => $params['leave_memo'],
            'order_id'      => $params['order_id'],
            'url'           => '/hr/leave/my',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'template_id'   => 221,
            'to_id'         => $params['user_id'],
            'title'         => "【请假申请驳回】申请人：{$params['user_name']}",
            'mobile'        => '',
            'mail'          => $params['receive_user_mail'],
        );
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 121;
        SendMessage::getInstance()->sendIm($mail_params);

        //hrer
        foreach($this->hrer as $hrer_id => $hrer_value){
            $hr_er = json_decode($hrer_value,true);
            $mail_params['to_id']= $hrer_id;
            $mail_params['mail'] = $hr_er['mail'];
            $mail_params['content']['receive_name'] = $hr_er['name'];
            $mail_params['content']['username'] = "{$params['user_name']}（{$params['depart_name']}）";
            $mail_params['content']['order_explains'] = '';
            $mail_params['template_id'] = 221;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 121;
            SendMessage::getInstance()->sendIm($mail_params);
        }

        //hrbp
        $mail_params['to_id']= $this->hrbp['user_id'];
        $mail_params['mail'] = $this->hrbp['mail'];
        $mail_params['content']['receive_name'] = $this->hrbp['user_name'];
        $mail_params['template_id'] = 221;
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 121;
        SendMessage::getInstance()->sendIm($mail_params);
    }

    /**
     * 撤销(hrer&hrbp)
     * @param string $params
     */
    private function _sendRevokeMessage($params){
        $content = array(
            'receive_name'  => $params['user_name'],
            'username'      => "{$params['user_name']}（{$params['depart_name']}）",
            'order_type'    => $params['order_type'],
            'leave_data'    => $params['leave_date'],
            'leave_days'    => $params['leave_days'],
            'leave_memo'    => $params['leave_memo'],
            'order_id'      => $params['order_id'],
            'url'           => '/hr/leave/approval',
        );
        $mail_params = array(
            'token_type'    => 'process',
            'content'       => $content,
            'title'         => "【请假申请撤销】申请人：{$params['user_name']}",
            'mobile'        => '',
        );

        //hrer
        foreach($this->hrer as $hrer_id => $hrer_value){
            $hr_er = json_decode($hrer_value,true);
            $mail_params['to_id']= $hrer_id;
            $mail_params['mail'] = $hr_er['mail'];
            $mail_params['content']['receive_name'] = $hr_er['name'];
            $mail_params['template_id'] = 220;
            SendMessage::getInstance()->sendMail($mail_params);
            $mail_params['template_id'] = 120;
            SendMessage::getInstance()->sendIm($mail_params);
        }

        //hrbp
        $mail_params['to_id']= $this->hrbp['user_id'];
        $mail_params['mail'] = $this->hrbp['mail'];
        $mail_params['content']['receive_name'] = $this->hrbp['user_name'];
        $mail_params['template_id'] = 220;
        SendMessage::getInstance()->sendMail($mail_params);
        $mail_params['template_id'] = 120;
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
                    'leave_date'    => $params['leave_date'],
                    'leave_days'    => $params['leave_days'],
                    'leave_memo'    => $params['leave_memo'],
                    'order_id'      => $params['order_id'],
                    'url'           => '/hr/leave/approval',
                );

                $mail_params = array(
                    'token_type'    => 'process',
                    'content'       => $content,
                    'template_id'   => 222,
                    'to_id'         => $d_l['user_id'],
                    'title'         => "【请假申请提醒】申请人：{$params['user_name']}",
                    'mobile'        => '',
                    'mail'          => $d_l['mail'],
                );
                SendMessage::getInstance()->sendMail($mail_params);
                $mail_params['template_id'] = 122;
                SendMessage::getInstance()->sendIm($mail_params);
            }
        }
    }
}