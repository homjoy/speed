<?php
namespace Admin\Modules\Meeting;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Meeting\Meeting;
use Admin\Package\Company\Office;
/**
 * 会议室管理首页
 * Class Home
 *
 * @package Admin\Modules\Meeting
 */
class Home extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
    private   $page_size = 20;
	//public static $VIEW_SWITCH_JSON = TRUE;
    protected $checkUserPermission = TRUE;
	public function run() {
	    $this->_init();
        //分页控制
        if(isset($this->params['page'])){
            if($this->params['page']<=0){
                $this->params['page'] =1;
            }
            $this->params['page_size'] = $this->page_size;
        }
        //获取所有的 会议室
        $query_count = $this->params;
        $query_count['count'] = 1;

        $temp_count =  Meeting::getInstance()->getMeetingList($query_count);

        if($temp_count >=0){
            $meeting_all = Meeting::getInstance()->getMeetingList($this->params);
        }

        //获取所有的 办公城市
        $office_list = Office::getInstance()->officeList( array('page_size' => 50));
        foreach($meeting_all as &$meet_value){
            $meet_value['office_position'] = isset($office_list[$meet_value['office_id']]) ?
                $office_list[$meet_value['office_id']]['office_position'] : '';
            $meet_value['status'] = ( $meet_value['status'] ==1 )  ?
               '可预订' : '不可预订';
        }
        unset($meet_value);
        $return = Response::gen_success($meeting_all);
        $return['count'] = ceil($temp_count/$this->page_size);
        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);


	}


	private function _init() {
		
		$this->rules = array(
			'status'  => array(
				'type'    => 'multiId',
				'default' => array(0,1), //0 无效 1有效
			),
            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'office_id'  => array(
                'type'    => 'integer',
            ),

		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}