<?php
namespace Admin\Modules\Structure\Outer;

use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;
use Admin\Package\Account\UserOutsourcingInfo;
/**
 * 主页信息
 * @author hongzhou@meilishuo.com
 * @since 2015-8-10 下午12:53:13
 */
class UserHome extends BaseModule {
	
	protected $errors = NULL;
	private   $params = NULL;
    private   $page_size = 20;
    private   $count = 1;
//    public static $VIEW_SWITCH_JSON = TRUE;
    protected $checkUserPermission = TRUE;
	public function run() {

	    $this->_init();

        //分页控制
        if(isset($this->params['page'])){
            if($this->params['page']<=0){
                $this->params['page'] =1;
            }
            $this->params['offset'] = intval($this->params['page']-1)* $this->page_size;
        }
        if (ord($this->params['search']) <= 122) {

            $is_mail = preg_match('/@/', $this->params['search']);

            if ($is_mail) { // 邮箱
                $this->params['search'] = explode("@",$this->params['search']);
                $this->params['mail']  = $this->params['search'][0];
            } else { // 英文名
                $this->params['name_en'] = $this->params['search'];
            }
        } else { // 中文名
            $this->params['name_cn'] = $this->params['search'];
        }

		$user_info = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo($this->params);

   		if ($user_info == FALSE) {
           $return =  Response::gen_success(array());
           $return['count'] ='';
           $return['page'] ='';
           return $this->app->response->setBody($return);
	    }

        $result = array();
        foreach ($user_info as $k => $v) {
            $res['user_id'] = isset($v['out_user_id'])?$v['out_user_id']:'';
            $res['staff_id'] = isset($v['staff_id'])?$v['staff_id']:'';
            $res['user_name'] = isset($v['name_cn'])?$v['name_cn']:'';
            $res['mail'] = isset($v['mail'])?$v['mail']:'';
            $res['mail_suffix'] = isset($v['mail_suffix'])?$v['mail_suffix']:'';
            $result[] = $res;
        }

        $return = Response::gen_success($result);//返回总数
        $temp_count = UserOutsourcingInfo::getInstance()->searchUserOutsourcingInfo(array('status'=>$this->params['status']
            ,'count'=>$this->count));
        $return['count'] = ceil($temp_count/$this->page_size);
        $return['page'] = $this->params['page'];
        return $this->app->response->setBody($return);

	}
	
	private function _init() {

		$this->rules = array(
			'status'  => array(
				'type'    => 'multiId',
				'default' => array(1,3),
			),
            'search'  => array(
                'type'    => 'string',
            ),
            'hire_end_time'  => array(
                'type'    => 'string',
            ),
            'hire_start_time'  => array(
                'type'    => 'string',
            ),
            'page'  => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'limit'  => array(
                'type'    => 'integer',
                'default' => 20,
            ),
            'offset'  => array(
                'type'    => 'integer',
                'default' => 0,
            ),
            'match'  => array(
                'type'    => 'string',
                'default' => 'like',
            ),
		);
        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
	}
	
}