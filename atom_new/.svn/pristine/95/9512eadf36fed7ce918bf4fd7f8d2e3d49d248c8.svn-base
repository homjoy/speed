<?php
namespace Atom\Package\Mail;

use Atom\Package\Account\UserInfo;
use Atom\Package\Account\UserPersonalInfo;
use Atom\Package\Company\City;
/**
 * Class MailBase
 * @package Atom\Package\Mail
 */
class MailBase{

    /**
     * 获取用户信息
     * @param $user_id
     * @return $all_status
     */
    public static function getUser($user_id,$all_status = false){
        if(empty($user_id)){
            return array();
        }
        $params = array();
        $params['user_id'] = $user_id;
        if($all_status){
            $params['status'] = array(1,2,3);
        }
        $user_info = UserInfo::model()->getDataList($params);
        if(empty($user_info)){
            return array();
        }
        $user_info = array_pop($user_info);
        $user_person = UserPersonalInfo::model()->getDataList($params);
        if(empty($user_person)){
            return array();
        }
        $user_person = array_pop($user_person);


        $user_info['mobile'] = $user_person['mobile'];
        return $user_info;
    }

    /**
     * 获取城市
     * @param $week
     * @return $result
     */
    public static function getCityName($city_id){
        $city_name = '';
        $city = City::getInstance()->getCityById($city_id);

        if(is_array($city) && !empty($city)){
            $city_name = $city['city_name'];
        }
        return $city_name;
    }

    /**
     * 获取星期
     * @param $week
     * @return $result
     */
    public static function getWeek($week = 1){
        $array = array(
            0 => '星期日',
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
            7 => '星期日',
        );
        return $array[$week];
    }

    /**
     * 获取会议室类型
     * @param $meeting_type
     * @return $result
     */
    public static function getMeetingType($meeting_type){
        $array = array(
            1 => '普通会议',
            2 => '视频会议',
            3 => '电话会议',
        );
        return $array[$meeting_type];
    }

    /**
     * 获取时间
     * @param $book_date
     * @param $start_time
     * @param $end_time
     * @param $week_day
     * @param $only_time
     * @return $result
     */
    public static function getTime($book_date,$start_time,$end_time,$week_day,$only_time = false){
        $s_t = strtotime($book_date.' '.$start_time);
        $e_t = strtotime($book_date.' '.$end_time);
        if($only_time){
            $result = date('H:i',$s_t);
        }else{
            $week_day = self::getWeek($week_day);
            $result = date('n月d日 ',$s_t).$week_day.'，'.date('H:i',$s_t).'-'.date('H:i',$e_t);
        }
        return $result;
    }
}
