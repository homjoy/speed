<?php
namespace Atom\Package\Migrate;
use Atom\Package\Company\City;
use Atom\Package\Company\Company;
use Atom\Package\Meeting\MeetingRoom;
use Atom\Package\Meeting\RoomServiceRule;

/**
 * Class MeetingRoomMigration
 * @package Atom\Package\Migrate
 */
class MeetingRoomMigration{

    /**
     * 生成城市数据
     */
    public static function city()
    {
        $cities = array(
            array('city_id'=> 1,'city_name'=>'北京','status'=>1),
            array('city_id'=> 3,'city_name'=>'上海','status'=>1),
            array('city_id'=> 5,'city_name'=>'杭州','status'=>1),
            array('city_id'=> 7,'city_name'=>'广州','status'=>1),
        );
        foreach($cities as $c)
        {
            City::getInstance()->insert($c);
        }
    }


    /**
     * 初始的公司数据.
     */
    public static function company()
    {
        $companies = array(
            array(
                'company_id' => 1,
                'city_id' => 1,
                'company_name' => '新中关',
                'company_address' => '新中关',
                'company_addr' => '',
                'telephone' => '',
                'fax' => '',
                'status' => 1,
            ),
            array(
                'company_id' => 3,
                'city_id' => 1,
                'company_name' => '远中悦莱',
                'company_address' => '远中悦莱',
                'company_addr' => '',
                'telephone' => '',
                'fax' => '',
                'status' => 1,
            ),
            array(
                'company_id' => 5,
                'city_id' => 3,
                'company_name' => '金虹桥',
                'company_address' => '金虹桥',
                'company_addr' => '',
                'telephone' => '',
                'fax' => '',
                'status' => 1,
            ),
            array(
                'company_id' => 7,
                'city_id' => 5,
                'company_name' => '浙商财富中心',
                'company_address' => '浙商财富中心',
                'company_addr' => '',
                'telephone' => '',
                'fax' => '',
                'status' => 1,
            ),
            array(
                'company_id' => 9,
                'city_id' => 7,
                'company_name' => '保利威座',
                'company_address' => '保利威座',
                'company_addr' => '',
                'telephone' => '',
                'fax' => '',
                'status' => 1,
            ),
        );

        foreach($companies as $c)
        {
            Company::getInstance()->insert($c);
        }
    }


    /**
     * 生成会议室数据
     * @throws \Exception
     */
    public static function rooms()
    {
        $rooms = Crab::model()->getMeetingRooms();
        $areaMap = array(
            '1' => 1,
            '2' => 3,
            '3' => 5,
            '4' => 7,
        );
        $companyMap = array(
            '1' => 1,
            '2' => 5,
            '3' => 7,
            '4' => 9,
        );
        foreach($rooms as $room)
        {
            if(in_array($room['room_no'],array('1007','8888'))){
                continue;
            }
            $companyId = $companyMap[$room['room_area']];
            if(in_array($room['room_no'],array('0301','0302'))){
                $companyId = 3;
            }
            $roomId = MeetingRoom::getInstance()->insert(array(
                'room_id'=> $room['room_id'],
                'city_id'=> $areaMap[$room['room_area']],
                'company_id'=> $companyId,
                'office_id'=> 1,
                'room_sn'=> $room['room_no'],
                'room_name'=> $room['room_name'],
                'room_position'=> intval(substr($room['room_no'],0,2)) .'层',
                'room_capacity'=> $room['room_capacity'],
                'extension'=> $room['extension'],
                'status'=> $room['status'],
                'room_detail'=> $room['room_detail'],
                'type'=> 1
            ));

            $devices = explode('、',$room['room_detail']);
            if(empty($devices)){
                continue;
            }

            $deviceMap = array(
                '视频会议' => 1,
                '投影仪' => 3,
                '电话会议' => 5,
            );
            foreach($devices as $d)
            {
                if(isset($deviceMap[$d])){
                    $data = array(
                        'room_id' => $roomId,
                        'service_id' => $deviceMap[$d],
                        'service_status' => 'ok',
                        'tips' => '',
                        'config' => '',
                        'status' => 1,
                    );
                    $ret = RoomServiceRule::model()->insertOrUpdate($data);
                }
            }
        }
    }


    /**
     * 新的会议室数据.
     * @throws \Exception
     */
    public static function newRooms()
    {
        $rooms = array(
            //北京-新中关
            array(
                'room_id'=> 19,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0601',
                'room_name'=> 'Hello Kitty',
                'room_position'=> '6层进门第一间',
                'room_capacity'=> 4,
                'extension'=> 6,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 21,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0602',
                'room_name'=> '马卡龙',
                'room_position'=> '6层进门第二间',
                'room_capacity'=> 12,
                'extension'=> 6,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),

            array(
                'room_id'=> 49,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0603',
                'room_name'=> '小竹林',
                'room_position'=> '6层出门右转',
                'room_capacity'=> 30,
                'extension'=> 6,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),


            array(
                'room_id'=> 15,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0901',
                'room_name'=> '第五大道',
                'room_position'=> '9层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 9,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 16,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0902',
                'room_name'=> '里原宿',
                'room_position'=> '9层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 9,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 14,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0903',
                'room_name'=> '香榭大道',
                'room_position'=> '9层进门第2排',
                'room_capacity'=> 12,
                'extension'=> 9,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 17,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '0904',
                'room_name'=> '米兰大道',
                'room_position'=> '9层进门第2排',
                'room_capacity'=> 8,
                'extension'=> 9,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),




            array(
                'room_id'=> 1,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1001',
                'room_name'=> '古着',
                'room_position'=> '10层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 10,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 2,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1002',
                'room_name'=> '英伦',
                'room_position'=> '10层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 10,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 3,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1003',
                'room_name'=> '森女',
                'room_position'=> '10层进门第2排',
                'room_capacity'=> 6,
                'extension'=> 10,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 4,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1004',
                'room_name'=> '朋克',
                'room_position'=> '10层进门第2排',
                'room_capacity'=> 6,
                'extension'=> 10,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 5,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1005',
                'room_name'=> '御姐',
                'room_position'=> '10层进门第2排',
                'room_capacity'=> 8,
                'extension'=> 10,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 6,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1006',
                'room_name'=> '女神',
                'room_position'=> '10层进门第3排',
                'room_capacity'=> 30,
                'extension'=> 10,
                'status'=> 0,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 23,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1007',
                'room_name'=> '咖啡厅',
                'room_position'=> '10层进门左转',
                'room_capacity'=> 30,
                'extension'=> 10,
                'status'=> 1,
                'room_detail'=> '投影仪、视频会议',
                'type'=> 1
            ),



            array(
                'room_id'=> 10,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1101',
                'room_name'=> '波普',
                'room_position'=> '11层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 11,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 11,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1102',
                'room_name'=> '嬉皮',
                'room_position'=> '11层进门第1排',
                'room_capacity'=> 6,
                'extension'=> 11,
                'status'=> 0,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 12,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1103',
                'room_name'=> '复古',
                'room_position'=> '11层进门第2排',
                'room_capacity'=> 6,
                'extension'=> 11,
                'status'=> 1,
                'room_detail'=> '投影仪',
                'type'=> 1
            ),
            array(
                'room_id'=> 13,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 1,
                'room_sn'=> '1104',
                'room_name'=> '巴洛克',
                'room_position'=> '11层进门第2排',
                'room_capacity'=> 12,
                'extension'=> 11,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),





            //北京-远中悦莱
            array(
                'room_id'=> 35,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 3,
                'room_sn'=> '0301',
                'room_name'=> '田园',
                'room_position'=> '',
                'room_capacity'=> 35,
                'extension'=> 3,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 37,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 3,
                'room_sn'=> '0302',
                'room_name'=> '瑞丽',
                'room_position'=> '',
                'room_capacity'=> 4,
                'extension'=> 3,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 51,
                'city_id'=> 1,
                'company_id'=> 1,
                'office_id'=> 3,
                'room_sn'=> '0303',
                'room_name'=> '阳光',
                'room_position'=> '',
                'room_capacity'=> 20,
                'extension'=> 3,
                'status'=> 1,
                'room_detail'=> '电话会议',
                'type'=> 1
            ),


            //杭州-浙商财富中心
            array(
                'room_id'=> 41,
                'city_id'=> 5,
                'company_id'=> 1,
                'office_id'=> 7,
                'room_sn'=> '0401',
                'room_name'=> '蘅芜苑',
                'room_position'=> '',
                'room_capacity'=> 8,
                'extension'=> 4,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 43,
                'city_id'=> 5,
                'company_id'=> 1,
                'office_id'=> 7,
                'room_sn'=> '0402',
                'room_name'=> '潇湘馆',
                'room_position'=> '',
                'room_capacity'=> 8,
                'extension'=> 4,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 27,
                'city_id'=> 5,
                'company_id'=> 1,
                'office_id'=> 7,
                'room_sn'=> '0403',
                'room_name'=> '藕香榭',
                'room_position'=> '',
                'room_capacity'=> 10,
                'extension'=> 4,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),


            //广州-保利威座
            array(
                'room_id'=> 25,
                'city_id'=> 7,
                'company_id'=> 1,
                'office_id'=> 9,
                'room_sn'=> '3201',
                'room_name'=> '雅典娜',
                'room_position'=> '',
                'room_capacity'=> 10,
                'extension'=> 32,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 39,
                'city_id'=> 7,
                'company_id'=> 1,
                'office_id'=> 9,
                'room_sn'=> '3202',
                'room_name'=> '小蛮腰',
                'room_position'=> '',
                'room_capacity'=> 10,
                'extension'=> 32,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),
            array(
                'room_id'=> 33,
                'city_id'=> 7,
                'company_id'=> 1,
                'office_id'=> 9,
                'room_sn'=> '3203',
                'room_name'=> '维多利亚',
                'room_position'=> '',
                'room_capacity'=> 10,
                'extension'=> 32,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议',
                'type'=> 1
            ),


            //上海-金虹桥
            array(
                'room_id'=> 29,
                'city_id'=> 3,
                'company_id'=> 1,
                'office_id'=> 5,
                'room_sn'=> '0901',
                'room_name'=> 'CHANEL',
                'room_position'=> '9层进门第1排',
                'room_capacity'=> 10,
                'extension'=> 10,
                'status'=> 1,
                'room_detail'=> '投影仪、电话会议、视频会议',
                'type'=> 1
            ),
        );


        foreach($rooms as $room)
        {
            $roomId = MeetingRoom::getInstance()->migrateInsert($room);
            //$roomId = $room['room_id'];

            $devices = explode('、',$room['room_detail']);
            if(empty($devices)){
                continue;
            }

            $deviceMap = array(
                '视频会议' => 1,
                '投影仪' => 3,
                '电话会议' => 5,
            );

            foreach($devices as $d)
            {
                if(isset($deviceMap[$d])){
                    $data = array(
                        'room_id' => $roomId,
                        'service_id' => $deviceMap[$d],
                        'service_status' => 'ok',
                        'tips' => '',
                        'config' => static::getRoomServiceConfig($roomId,$deviceMap[$d]),
                        'status' => 1,
                    );
                    $ret = RoomServiceRule::model()->insertOrUpdate($data);
                }
            }

            //加入默认的待客服务.
            $ret = RoomServiceRule::model()->insertOrUpdate(array(
                'room_id' => $roomId,
                'service_id' => 7, //待客服务.
                'service_status' => 'ok',
                'tips' => '',
                'config' => static::getRoomServiceConfig($roomId,7),
                'status' => 1,
            ));
        }
    }

    private static function getRoomServiceConfig($roomId,$serviceId)
    {
        //视频服务走默认.
        if($serviceId == 1)
        {
            return '';
        }
        $configs = array(
            '{"user":[2667,2699],"time_start":[-10],"time_end":[-10]}' => array(
                array(19, 3,),
                array(19, 5,),
                array(19, 7,),
                array(21, 3,),
                array(21, 5,),
                array(21, 7,),
                array(49, 3,),
                array(49, 5,),
                array(49, 7,),
            ),
            '{"user":[2667,2607],"time_start":[-10],"time_end":[-10]}' => array(
                array(14, 3),
                array(14, 5),
                array(14, 7),
                array(15, 3),
                array(15, 5),
                array(15, 7),
                array(16, 3),
                array(16, 5),
                array(16, 7),
                array(17, 3),
                array(17, 5),
                array(17, 7),
            ),

            '{"user":[2667,809],"time_start":[-10],"time_end":[-10]}' => array(
                array(1, 3),
                array(1, 5),
                array(1, 7),
                array(2, 3),
                array(2, 5),
                array(2, 7),
                array(3, 3),
                array(3, 5),
                array(3, 7),
                array(4, 3),
                array(4, 5),
                array(4, 7),
                array(5, 3),
                array(5, 5),
                array(5, 7),
                array(6, 3),
                array(6, 5),
                array(6, 7),
                array(45, 3),
                array(45, 5),
                array(45, 7),
            ),

            '{"user":[2667,2335,3201],"time_start":[-10],"time_end":[-10]}' => array(
                array(10, 3),
                array(10, 5),
                array(10, 7),
                array(11, 3),
                array(11, 5),
                array(11, 7),
                array(12, 3),
                array(12, 5),
                array(12, 7),
            ),
            '{"user":[919],"time_start":[-10],"time_end":[-10]}' => array(
                array(27, 3),
                array(27, 5),
                array(27, 7),
                array(41, 3),
                array(41, 5),
                array(41, 7),
                array(43, 3),
                array(43, 5),
                array(43, 7),
            ),

            '{"user":[2255],"time_start":[-10],"time_end":[-10]}' => array(
                array(25, 3),
                array(25, 5),
                array(25, 7),
                array(33, 3),
                array(33, 5),
                array(33, 7),
                array(39, 3),
                array(39, 5),
                array(39, 7),
            ),


            '{"user":[1371],"time_start":[-10],"time_end":[-10]}' => array(
                array(29, 3),
                array(29, 5),
                array(29, 7),
            ),

            '{"user":[2813],"time_start":[-10],"time_end":[-10]}' => array(
                array(35, 3),
                array(35, 5),
                array(35, 7),
                array(37, 3),
                array(37, 5),
                array(37, 7),
                array(51, 3),
                array(51, 5),
                array(51, 7)
            ),
        );
        foreach($configs as $cfg => $list)
        {
            foreach($list as $l)
            {
                if($l[0] == $roomId && $serviceId == $l[1]){
                    return $cfg;
                }
            }
        }

        return '';
    }
}