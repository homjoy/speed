<?php
namespace Atom\Scripts\User;

use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserAvatar;
use Atom\Package\Account\UserPersonalInfo;
use Atom\Package\Account\DepartmentInfo;
use Libs\Util\ArrayUtilities;
use PHPQRCode\QRcode;

/**
 * 生成二维码
 * Class CreateQrCode
 * @package Atom\Scripts\User
 */

class CreateQrCode extends \Frame\Script
{
    public static $timeRange = 61; //60秒

    public function run()
    {
        defined('UPLOAD_PATH') || define("UPLOAD_PATH", '/home/work/uploads/');

        global $start;
        $now = date('Y-m-d H:i:s', $start);
        echo $now, "\n";

        //获取更新的用户信息
        $userInfo = self::_getNewUserInfo($start);

        //获取用户id
        $uids = ArrayUtilities::my_array_column($userInfo, 'user_id');
        $personalInfo = self::_getPersonalInfo($uids);

        //部门数据
        $departids = ArrayUtilities::my_array_column($userInfo, 'depart_id');
        $departInfo = self::_getDepartInfo($departids);

        foreach ($userInfo as $key => $value) {
            $user_id    = $value['user_id'];
            $depart_id  = $value['depart_id'];

            $value = array_merge($value, $personalInfo[$key], $departInfo[$depart_id]);

            $userInfo[$key] = $value;
        }

        //生成二维码
        if (!empty($userInfo)) {
            foreach ($userInfo as $key => $value) {

                self::genImage($value);
            }
        }

        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //获取新用户信息
    public static function _getNewUserInfo($time = 0){

        if (empty($time)) {
            $time = time();
        }

        $time -= self::$timeRange;
        $updateTime = date('Y-m-d H:i:s', $time);

        $queryParam = array(
            'update_time' => $updateTime,
        );

        return UserInfo::model()->getDataList($queryParam, 0, 99);
    }

    //获取一段时间内更新头像的用户
    public static function _getAvatarInfo($uids = array()){

        if (empty($uids)) {
            return FALSE;
        }

        $queryParam = array(
            'user_id' => $uids,
            'status'  => array(1),
        );

        return UserAvatar::model()->getDataList($queryParam, 0, 99);
    }

    //获取用户信息
    public static function _getPersonalInfo($uids = array()){

        if (empty($uids)) {
            return FALSE;
        }

        $queryParam = array(
            'user_id' => $uids,
        );

        return UserPersonalInfo::model()->getDataList($queryParam, 0, 99);
    }

    //获取部门信息
    public static function _getDepartInfo($departids = array()){

        if (empty($departids)) {
            return FALSE;
        }

        $queryParam = array(
            'depart_id' => $departids,
        );

        return DepartmentInfo::model()->getDataList($queryParam, 0, 99);
    }

    //生成二维码
    public static function genImage($userInfo = array()){
        if (empty($userInfo)) {
            return FALSE;
        }

        if(strstr($userInfo['mobile'], '/')){
            $telArr = explode('/', $userInfo['mobile']);
        }else{
            $telArr[0] = $userInfo['mobile'];
            $telArr[1] = '';
        }

//二维码内容
$qrString = <<<QRCODE
BEGIN:VCARD
FN:{$userInfo['name_cn']}
TITLE:{$userInfo['depart_name']}
ORG:美丽说
TEL;CELL:{$telArr[0]}
TEL;WORK:{$telArr[1]}
EMAIL;WORK:{$userInfo['mail']}@meilishuo.com
END:VCARD";
QRCODE;

        //二维码路径
        $qrPath = UPLOAD_PATH . 'qrcode' . '/' . $userInfo['user_id'] . '.png';

        //判断图片是否存在
        if (!file_exists($qrPath)) {
            $res = QRcode::png($qrString, $qrPath, 'L', 4, 2);
            echo "{$userInfo['user_id']} {$userInfo['name_cn']} \n";
        }

        return TRUE;
    }

}
