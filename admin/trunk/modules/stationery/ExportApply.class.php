<?php
namespace Admin\Modules\Hr\Leave;

use Admin\Modules\Common\BaseModule;
use Admin\Modules\Common\ExportExcel;
use Admin\Package\Core\Config;
use Admin\Package\Account\UserInfo;
use Admin\Package\Department\Department;
use  Libs\Util\ArrayUtilities;
/**
 *   hongzhou@meilishuo.com
 * User: MLS  ExportLeaveList
 * Date: 15/11/13
 * Time: 下午12:18
 */
class ExportApply extends BaseModule{

    protected $errors = null;
    private $params = null;
    public static $VIEW_SWITCH_JSON = TRUE;

    protected $config = array (
        'filename' => '申请物品导出',
        'config'   => array (


        )
    );
    
    public function run()
    {
        $this->_init();

        //参数校验
        if (empty($this->params['create_time']) && empty($this->params['end_time'])) {
            echo "请选择开始和结束时间";
            exit;
        }
        //时间控制就算了吧 哥已经做了多次请求了
        if( abs((strtotime($this->params['end_time']) - strtotime($this->params['create_time']))) >365*86400 ){
            echo "导出天数超过365天";
            exit;
        }


        exit;
        
    }



    private function _init()
    {

        $this->rules = array(
           
            'create_time' => array(
                'type' => 'string',
                'required'	=> true,
            ),
            'end_time' => array(
                'type' => 'string',
                'required'	=> true,
            ),
//            'user_id' => array(
//                'type' => 'integer',
//            )
        );

        $this->params = $this->query()->safe();
        $this->errors = $this->query()->getErrors();
    }



}