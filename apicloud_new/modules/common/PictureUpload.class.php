<?php
namespace Apicloud\Modules\Common;

use Apicloud\Modules\Common\BaseModule;
use Apicloud\Package\Common\Response;
use Frame\Speed\Exception\ParameterException;
/**
 * Class PictureUpload
 * @package Apicloud\Modules\Common
 */
class PictureUpload extends BaseModule{

    private $targetFolder = '/home/work/uploads/avatar/_o/';

    protected $params = array();
    private $file_name = '';

	public function run() {
        //参数获取
        $this->_init();
        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $this->targetFolder;
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $arr = explode('.', $_FILES['Filedata']['name']);
            $type = array_pop($arr);
            $this->file_name = $this->params['user_id'] . '_' . date("YmdHis") . "." . $type;
            //请假业务上传用户提交的文件名称
            if($this->params['type'] == 'hr_leave'){
                $this->file_name = $this->params['user_id'].'_'.date("YmdHis").'|'.$_FILES['Filedata']['name'];
            }
            $targetFile = rtrim($targetPath,'/') . '/' . $this->file_name;

            move_uploaded_file($tempFile,$targetFile);
            chmod($targetFile, 0777);
            $avatar_url = $this->targetFolder . $this->file_name;
            if(extension_loaded('imagick')&&$_FILES['Filedata']['size']>10*1024*1024){
                $cmd = sprintf("/usr/local/bin/convert -quality 85 -strip %s %s",$targetFile,$targetFile);
                $rtn = exec($cmd);
            }

            if($this->params['type'] == 'hr_leave'){  //如果是请假附件则返回文件名
                $avatar_url = $this->file_name;
            }

            $data = array(
                'data' => $avatar_url,
            );

        }else{
            throw new ParameterException("没有选择上传文件！");
        }
		
		$this->app->response->setBody($data);
	}

    /**
     * 参数初始化
     */
    protected function _init(){
        $this->rules = array(
            'type' => array(
                'type'  => 'string',
                'default' => 'avatar',
            ),
        );
        $this->params = $this->post()->safe();
        $this->params['user_id'] = $this->app->currentUser['user_id'];

        if($this->params['type'] == 'avatar'){//头像
            $this->targetFolder = '/home/work/uploads/avatar/_o/';
        }elseif($this->params['type'] == 'hr_leave'){//请假附件
            $this->targetFolder = '/home/work/uploads/speed/hr_leave/';
        }
    }
}