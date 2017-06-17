<?php
namespace Atom\Tests\Meeting;


/**
 *
 * Class RoomServiceTest
 * @package Atom\Tests\Meeting
 */
class RoomServiceTest extends \Frame\Speed\Test\ApiTest {

    /**
     * 测试查询接口
     */
    public function testGet() {
        //查询参数
        $serviceId = array(1,3,5,7,9);
        $params = array(
            'service_id' => implode(',',$serviceId),
        );
        //删除
        $response = $this->atom('meeting/room_service_get',$params);
        $response->assertSuccess();
        $response->assertDataCount(count($serviceId));

        //查询不存在的
        $serviceId[] = 2;
        $response = $this->atom('meeting/room_service_get',$params);
        $response->assertSuccess();
        $response->assertDataCount(count($serviceId)-1);
    }


    /**
     * 测试添加接口
     * @throws \Frame\Speed\Exception\ApiException
     * @throws \Frame\Speed\Exception\AssertException
     */
    public function testAdd(){
        $service = array(
            'name' => 'RoomServiceTest::testAdd测试',
            'description' => 'RoomServiceTest::testAdd测试',
            'multizone' => 0,
            'type' => 0,
            'config' => '',
            'status' => 1,
        );

        //超过长度
        $service['name'] = 'RoomServiceTest::testAdd测试RoomServiceTest::testAdd测试RoomServiceTest::testAdd测试';
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertFailed();

        $service['description'] = 'RoomServiceTest::testAdd测试'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'RoomServiceTest::testAdd'
            .'测试RoomServiceTest::testAdd测试';
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertFailed();

        $service['multizone'] = 3;
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertFailed();

        $service['status'] = 3;
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertFailed();


        $service = array(
            'name' => 'RoomServiceTest::testAdd测试',
            'description' => 'RoomServiceTest::testAdd测试',
            'multizone' => 0,
            'type' => 0,
            'config' => '',
            'status' => 1,
        );
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertSuccess();
        $id = $response->getData();
    }


    public function testUpdate()
    {
        $service = array(
            'name' => 'RoomServiceTest::testAdd测试',
            'description' => 'RoomServiceTest::testAdd测试',
            'multizone' => 0,
            'type' => 0,
            'config' => '',
            'status' => 1,
        );
        $response = $this->atom('meeting/room_service_add',$service);
        $response->assertSuccess();

        $serviceId = $response->getData();
        $service = array(
            'service_id' => $serviceId,
            'name' => 'RoomServiceTest::testAdd测试111',
            'description' => 'RoomServiceTest::testAdd测试',
            'multizone' => 0,
            'type' => 0,
            'config' => '',
            'status' => 1,
        );
        $response = $this->atom('meeting/room_service_update',$service);
        $response->assertSuccess();

        $response = $this->atom('meeting/room_service_get',array('service_id'=>$serviceId));
        $response->assertSuccess();
        $response->assertDataEqual(array($service));
    }


    /**
     * 测试删除接口
     * @throws \Frame\Speed\Exception\ApiException
     * @throws \Frame\Speed\Exception\AssertException
     */
    public function testDelete()
    {
        $service = array(
            'name' => 'RoomServiceTest::testAdd测试',
            'description' => 'RoomServiceTest::testAdd测试',
            'multizone' => 0,
            'type' => 0,
            'config' => '',
            'status' => 1,
        );

        $serviceIds = array();
        for($i = 0; $i < 3; $i++){
            $data = $service;
            $data['name'] = $service['name'].$i;
            $response = $this->atom('meeting/room_service_add',$data);
            $response->assertSuccess();
            $serviceIds[] = intval($response->getData());
        }

        $idString= implode(',',$serviceIds);
        $params = array(
            'service_id' => $idString,
        );
        //删除
        $response = $this->atom('meeting/room_service_delete',$params);
        $response->assertSuccess();

        //查询删除列表
        $params = array(
            'service_id' => $idString,
            'status' => 0,
        );
        $response = $this->atom('meeting/room_service_get',$params);
        $response->assertSuccess();
        $response->assertDataCount(count($serviceIds));
    }
}
