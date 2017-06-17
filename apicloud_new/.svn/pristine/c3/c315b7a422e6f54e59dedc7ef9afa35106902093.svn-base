<?php
namespace Apicloud\Modules\Contacts;

use Apicloud\Modules\Common\BaseModule;
use Apicloud\Package\Common\Response;
use Libs\Util\ArrayUtilities;
use Frame\Speed\Lib\Api;
use \Libs\Sphinx\curl;
use Frame\Speed\Exception\ParameterException;
use Apicloud\Package\Passport\Mycrypt3DES;

use Apicloud\Package\Contacts\MGJListSearch;
use Apicloud\Package\Contacts\MLSListSearch;

use Apicloud\Package\Account\UserPersonalInfo;
use Apicloud\Package\Account\UserInfoList;
use Apicloud\Package\Account\UserAvatar;
use Apicloud\Package\Account\MDAvtar;
use Apicloud\Package\Department\DepartmentInfo;
use Apicloud\Package\Department\DepartmentRelation;

/**
 * 搜索通讯录
 * @author minggeng
 * @date 2016-3-26 下午12:47:39
 */
class MulitySearch extends BaseModule {

	private $number = NULL;
	private $english = NULL;
	private $chinese = NULL;
    protected $params = array();
	
	private $data = array();
	
	public function run() {
		$this->_init();

		if(!$this->_part()) {
            throw new ParameterException('参数不合法！');
		}

        $mls_list = MLSListSearch::getInstance()->getMLSInfo($this->params['q']);
        $this->_doAppend($mls_list);

        $mgj_list = MGJListSearch::getInstance()->getMGJInfo($this->params['q']);
        $this->_doAppend($mgj_list);

        //排序&处理手机号
        $result = array();
		if(is_array($this->data) && !empty($this->data)){
			foreach ($this->data as $k => $v) {
				//$v['speed_im'] = base64_encode($v['user_id']); //speed_im
				//$v['qrcode'] = 'http://uploads.speed.meilishuo.com/qrcode/' . $v['user_id'] . '.png'; // 二维码
				//手机号特殊处理
                if($this->params['source']=='pc'){
                    $v['mobile_encry'] = substr($v['mobile'],0,3).'&bull;&bull;&bull;&bull;&bull;&bull;'.substr($v['mobile'],7,11);
                }
                $this->data[$k] = $v;
                $result[] = $v;
			}
		}
        $this->app->response->setBody($result);
	}

	/**
	 * 合并结果
	 * @param array $data
	 * @return boolean
	 */
	private function _doAppend($data = array()) {
		if(!$data || empty($data) || !is_array($data) || isset($data['error_code'])) {
			return FALSE;
		}

        $this->data = $this->data + $data; //前提是user_id作为数组下标
	}
	
	//分析输入字符
	private function _part() {

		$this->params['q'] = strtok($this->params['q'], '@');
		
		if(preg_match('/^[0-9]+$/', $this->params['q']) && strlen($this->params['q']) > 3) { //4个数字
			$this->number = $this->params['q'];
        }else if(preg_match('/^[a-zA-Z0-9]+$/', $this->params['q']) && strlen($this->params['q']) > 2) { //3个英文
			$this->english = $this->params['q'];
		}else if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $this->params['q']) && strlen($this->params['q']) > 2) { //1个汉字
			$this->chinese = $this->params['q'];
		}else {
			return FALSE;
		}
		return TRUE;
	}

	private function _init() {
        $this->rules = array(
            'source'=>array(
                'type'=>'string',
                'default' => 'pc',
            ),
            'q'=>array(
                'required'=> true,
                'type'=>'string',
            ),
        );

        $this->params   = $this->query()->safe();
        $this->params['q'] = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $this->params['q']);
        $this->errors   = $this->query()->getErrors();
    }

}
