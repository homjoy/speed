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

class CreateQrCodeWithAvatar extends \Frame\Script
{
    public static $timeRange = 61; //60秒

    public function run()
    {
        defined('UPLOAD_PATH') || define("UPLOAD_PATH", '/home/work/uploads/');

        global $start;
        $now = date('Y-m-d H:i:s', $start);
        echo $now, "\n";

        //头像信息
        $avatars = self::_getAvatarInfo($start);
        //获取用户信息
        $uids = ArrayUtilities::my_array_column($avatars, 'user_id');
        $userInfo = self::_getUserInfo($uids);

        //生成二维码
        if (!empty($avatars) && !empty($userInfo)) {
            foreach ($avatars as $key => $value) {
                $uid = $value['user_id'];

                $value = array_merge($value, $userInfo[$uid]);

                self::genImage($value);
            }
        }

        $end = microtime(true);
        $total = $end-$start;
        $this->app->response->setBody("开始：{$start}，结束：{$end}，总用时：{$total}秒。\n");
    }

    //获取一段时间内更新头像的用户
    public static function _getAvatarInfo($time = 0){

        if (empty($time)) {
            $time = time();
        }

        $time -= self::$timeRange;
        $updateTime = date('Y-m-d H:i:s', $time);

        $queryParam = array(
            'update_time' => $updateTime,
        );

        return UserAvatar::model()->getDataList($queryParam, 0, 99);
    }

    //获取用户信息
    public static function _getUserInfo($uids = array()){

        if (empty($uids)) {
            return FALSE;
        }

        $queryParam = array(
            'user_id' => $uids,
            'status'  => array(1,2,3),
        );

        $userInfo           = UserInfo::model()->getDataList($queryParam, 0, 99);
        $userPersonalInfo   = UserPersonalInfo::model()->getDataList($queryParam, 0, 99);

        //部门数据
        $departids  = ArrayUtilities::my_array_column($userInfo, 'depart_id');
        $departInfo = self::_getDepartInfo($departids);

        foreach ($userInfo as $key => $value) {
            $depart_id = $value['depart_id'];

            $value = array_merge($value, $userPersonalInfo[$key], $departInfo[$depart_id]);

            $userInfo[$key] = $value;
        }

        return $userInfo;
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

        $res = QRcode::png($qrString, $qrPath, 'L', 4, 2);

        $avatar = empty($userInfo['local_small']) ? 'http://172.16.0.20/' . $userInfo['avatar_small'] : $userInfo['local_small'];

        if (!empty($avatar)) {
            //带头像的二维码路径
            self::appentAvatarToQrcode($qrPath, $qrPath, $avatar);
        }

        echo "{$userInfo['user_id']} {$userInfo['name_cn']} \n";
        return TRUE;
    }

    //添加头像
    public static function appentAvatarToQrcode($qrPath, $imgPath, $avatar){

        if (empty($qrPath) || empty($imgPath) || empty($avatar)) {
            return FALSE;
        }

        //判断源二维码 与 头像是否存在
        if (!file_exists($qrPath) || !file_exists($avatar)) {
            //return FALSE;
        }
        //echo $qrPath, $imgPath, $avatar, "\n";
        //获取二维码
        $QR = @imagecreatefrompng($qrPath);
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        //获取头像
        $avatar = @imagecreatefromstring(file_get_contents($avatar));
        $avatar_width = imagesx($avatar);
        $avatar_height = imagesy($avatar);

        $logo_qr_width = $QR_width / 5;
        $scale = $avatar_width / $logo_qr_width;
        $logo_qr_height = $avatar_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;

        imagecopyresampled($QR, $avatar, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $avatar_width, $avatar_height);

        //header('Content-type: image/png');
        imagepng($QR, $imgPath);
        imagedestroy($QR);
    }
}
