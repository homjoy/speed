<?php 
namespace Admin\Modules\Workflow\User;

/**
 * 检索用户信息
 * @author jingjingzhang@meilishuo.com
 * @since 2015-07-14
 */
use Admin\Modules\Common\BaseModule;
use Admin\Package\Common\Response;

class AjaxUserSearch extends BaseModule {

	public static $VIEW_SWITCH_JSON = TRUE;
	protected $keywords = '';

	public function run() {

		if (!$this->_init()){
			return FALSE;
		}
	
		$param['status'] = 1;
		$param['match'] = 'LIKE';
		$param['all'] = 1;
		if (ord($this->keywords) <= 122) {
			$param['name_en'] = $this->keywords;
		} else {
			$param['name_cn'] = $this->keywords;
		}

		$userInfo = $this->searchUser($param);

		if (empty($userInfo) && ord($this->keywords) <= 122) {
			unset($param['name_en']);
        	$mail_prefix = explode('@', $this->keywords);
        	$param['mail'] = $mail_prefix[0];
        	$userInfo = $this->searchUser($param);
        }

        $result = array();
		if (!empty($userInfo)) {

			$departInfo = self::getClient()->call('atom', 'department/depart_info_list', array('status' => 1, 'all' => 1));
        	$departInfo = $this->parseApiData($departInfo);
        	$depart = array();
	        if (!empty($departInfo)) {
	        	foreach ($departInfo as $d) {
	        		$depart[$d['depart_id']] = $d['depart_name'];
	        	}
	        }

            foreach ($userInfo as $key => $value) {
            
                $tmp = array();
                $tmp['id'] = $value['user_id'];
                $departid = $value['depart_id'];

        		$departname = isset($depart[$departid]) ? '-' . $depart[$departid] : '';
                $tmp['name'] = $value['name_cn'] . $departname;
                $tmp['mail'] = $value['mail'];
   
                $result[] = $tmp;
            }
        }

        if (empty($result)) {
        	$return = Response::gen_error(50002);
        } else {
        	$return = Response::gen_success($result);
        }

        $this->app->response->setBody($return);
	}

	private function searchUser($param) {

		$ret = self::getClient()->call('atom', 'account/search_user_info', $param);
		$ret = $this->parseApiData($ret);
		return $ret;
	}

	private function _init() {

		$key = $this->request->GET;

		if (empty($key)) {
			return FALSE;
		} else {
			if (isset($key['term'])) {
				$this->keywords = $key['term'];
			} else if (isset($key['search'])) {
				$this->keywords = $key['search'];
			}
			
			return TRUE;
		}
	}
}

