<?php
namespace Atom\Modules\Core;

use Libs\Util\Format;
use Atom\Package\Common\Response;
use Atom\Package\Core\Menu;
use Atom\Package\Migrate\Crab;
/**
 * 获取菜单信息列表
 * @author haibinzhou@meilishuo.com
 * @since 2015-11-18
 */

class MenuList extends \Atom\Modules\Common\BaseModule {

	protected $params   = array();
	private $sample;
    protected $queryParams = array();
	
	public function run() {
		$this->_init();
		$this->sample   = Menu::model()->getFields();

        foreach($this->params as $key=>$data){
            if(!empty($data)){
                $this->queryParams[$key] = $data;
            }
        }

        if(empty($this->queryParams)){
            $return = Response::gen_error(10001);
            return $this->app->response->setBody($return);
        }

        $result = Menu::model()->getDataList($this->queryParams);
        if(!empty($result)){
            foreach($result as $k => $r){
                if($r['title'] == '打卡'){
                    //判断是否有打卡权限
                    $is_punch = $this->_isPunch();
                    if(!$is_punch){
                        unset($result[$k]);
                    }
                }
            }
        }

        if ($result === FALSE) {
        	$return = Response::gen_error(50001);
        }elseif (empty($result)) {
        	$return = Response::gen_error(30001);
        }else{
        	$return = Response::gen_success(Format::outputData($result, $this->sample, TRUE));
        }
    	$this->app->response->setBody($return);
	}

    private function _isPunch(){
        $putch_userids = Crab::model()->getPunchAllUsers();
        $putch_departids = Crab::model()->getPunchAllDeparts();
        if(array_key_exists($this->params['user_id'], $putch_userids)||array_key_exists($this->params['depart_id'], $putch_departids)) {
            return true;
        }
        return false;
    }

	/**
	 * 参数初始化
	 */
	protected function _init(){
		$this->rules = array(
			'id'=>array(
				'type'=>'multiId',
                'default' => '',
			),
			'parent_id'=>array(
				'type'=>'multiId',
			),
			'title'=>array(
				'type'=>'string',
			),
            'match' => array(    //传’like‘和’equal‘ 配合title进行模糊查询
                'type' => 'string',
            ),
            'status' => array(
                'type'=> 'multiId',
            ),
            'user_id' => array(
                'type'=> 'integer',
            ),
            'depart_id' => array(
                'type'=> 'integer',
            ),
		);
		$this->params   = $this->query()->safe();
	}

}
