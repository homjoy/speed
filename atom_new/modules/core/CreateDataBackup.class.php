<?php
namespace Atom\Modules\Core;

use Atom\Modules\Common\BaseModule;
use Atom\Package\Common\Response;
use Atom\Package\Core\DataBackup;
use Atom\Package\Account\DepartmentRelation;
use Atom\Package\Account\DepartmentSub;
use Atom\Package\Account\DepartmentInfo;
/**
 * 添加数据备份信息
 * @author haibinzhou@meilishuo.com
 * @date 2015-09-24
 */
class CreateDataBackup extends BaseModule {
	
	private $params = array();

	public function run() {
		
		$this->_init();
		if($this->query()->hasError()){
        	$return = Response::gen_error(10001, '', $this->query()->getErrors());
        	return $this->app->response->setBody($return);
        }

        $this->syncBackupInfo($this->params);
        $this->syncBackupRelation($this->params);
        $result = $this->syncBackupSub($this->params);

		if($result === FALSE) {
			$return = Response::gen_error(10004);
		}else {
			$return = Response::gen_success($result);
		}
		
		$this->app->response->setBody($return);
	}

    /**
     * 备份deparment_info
     */
    protected function syncBackupInfo($params = array()){
        $info          = array();
        $all['all']    = 1;
        $backup_info   = DepartmentInfo::model()->getDataList($all);
        $info['table'] = 'department_info';
        $info['data']  = json_encode($backup_info);

        $info['title'] = isset($temp['title'])?$temp['title']:'';


        DataBackup::model()->insert($info);
    }

    /**
     * 备份deparment_relation
     */
    protected function syncBackupRelation($params = array()){
        $info          = array();
        $all['all']    = 1;
        $backup_info   = DepartmentRelation::model()->getDataList($all);
        $info['table'] = 'department_relation';
        $info['data']  = json_encode($backup_info);
        $info['title'] = $params['title'];
        DataBackup::model()->insert($info);
    }

    /**
     * 备份deparment_sub
     */
    protected function syncBackupSub($params = array()){
        $info          = array();
        $all['all']    = 1;
        $backup_info   = DepartmentSub::model()->getDataList($all);
        $info['table'] = 'department_sub';
        $info['data']  = json_encode($backup_info);
        $info['title'] = $params['title'];
        return DataBackup::model()->insert($info);
    }
	
	private function _init() {
		$post = $this->request->GET;
		
		$this->rules = array(
			'table' => array(
				'type'=>'string',
			),
			'data' => array(
				'type'=>'string',
			),
            'title' => array(
                'type'=>'string',
            ),
            'type' => array(
                'type'=>'integer',
            ),
			'update_time' => array(
				'type'=>'string',
			),
		);
		
		$safe_post = $this->query()->safe();
		$this->params = array_intersect_key($safe_post, $post);
		return TRUE;
	}
	
}