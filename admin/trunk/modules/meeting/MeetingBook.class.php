<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\Meeting;
use Admin\Package\Company\Office;
use Admin\Package\Meeting\Book;
use Admin\Package\Account\UserInfo;
/**
 * 会议室预定查询
 * Class
 *
 * @package Admin\Modules\Meeting
 */
class MeetingBook extends BaseModule {
// 带出会议室信息 带出时间日期
    protected $errors = NULL;
    private   $params = NULL;
    private   $page_size = 20;
//    public static $VIEW_SWITCH_JSON = TRUE;
//    protected $checkUserPermission = TRUE;
    public function run() {

        $this->_init();

        if(empty($this->params['room_id'])){
            return $this->app->response->setBody(array());
        }
        //默认1个月
        if(empty($this->params['start_time'])&& empty($this->params['end_time'])){
           $this->params['start_time']= @date('Y-m-d',time()-5*24*60*60);
           $this->params['end_time']= @date('Y-m-d',time());
        }

        //查找预订相关信息
        $book_list =array();
        $book_list =  Book::getInstance()->getRoomBooks(
            array(
                'room_id'=>$this->params['room_id'],
                'book_start'=>$this->params['start_time'],
                'book_end'=>$this->params['end_time']
            ));
        $count =ceil(count($book_list)/$this->page_size);
        $book_list =array_slice($book_list,$this->page_size*($this->params['page']-1),$this->page_size*$this->params['page']);
        //得到相关人的信息
        $weekarray=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
        $data=array();
        if(is_array($book_list)&& !empty($book_list)){
            foreach($book_list as $key => $value){

                $tmp = array();
                $tmp['book_id'] = $value['book_id'];
                $tmp['user_id'] = $value['user_id'];
                $tmp['topic'] = $value['meeting_topic'];
                $tmp['start_time'] = @date('Y-m-d H:i',strtotime($value['book_start']));
                $tmp['end_time'] = @date('Y-m-d H:i',strtotime($value['book_end']));
                $tmp['start_w'] = $weekarray[$value['week_day']-1];//得到星期
                switch ($value['repeat_type']) {
                    case 1:
                        $tmp['is_repeat']='day';
                        break;
                    case 7:
                        $tmp['is_repeat']='week';
                        break;
                    case 30:
                        $tmp['is_repeat']='month';
                        break;
                    default:
                        $tmp['is_repeat']= '';
                }
                $user_tmp = UserInfo::getInstance()->getUserInfo(array('user_id'=>$tmp['user_id'],'status'=>array(1,2,3)));//取出name_cn

                $user_tmp = is_array($user_tmp)?array_pop($user_tmp):'';
                $tmp['name_cn'] =isset($user_tmp['name_cn'])?$user_tmp['name_cn']:'';
                $data[] = $tmp;
            }
        }
        // 查找room相关信息
        $room_info=array();
        $room_info = Meeting::getInstance()->meetingRoomGet(array('room_id'=>$this->params['room_id']));
        //过滤变量room_info

        $return=array(
            'room_name'=>$room_info['room_name'],
            'room_position'=>$room_info['room_position'],
            'room_capacity'=>$room_info['room_capacity'],
            'start_time' =>$this->params['start_time'],
            'end_time' =>$this->params['end_time'],
        );

        $return = Response::gen_success($return);

        $return['room_list'] =$data;
        $return['count']  =$count;
        $return['page']  =$this->params['page'];
        $this->app->response->setBody($return);
    }

    private function _init() {

        $this->rules = array(
            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'start_time'  => array(
                'type'    => 'datetime',
            ),
            'end_time'  => array(
                'type'    => 'datetime',
            ),
            'room_id'  => array(
                'type'    => 'integer',
            ),

        );
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }



}