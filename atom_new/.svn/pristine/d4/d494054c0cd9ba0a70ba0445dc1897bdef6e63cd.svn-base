<?php
namespace Atom\Tests\Meeting;


/**
 * 会议预定测试
 * Class MeetingBook
 * @package Atom\Tests\Meeting
 */
class MeetingBookTest extends \Frame\Speed\Test\ApiTest {

    /**
     * 测试查询接口
     */
    public function testGet() {

    }



    /**
     * 测试添加接口
     * @throws \Frame\Speed\Exception\ApiException
     * @throws \Frame\Speed\Exception\AssertException
     */
    public function testAdd(){
        $tomorrow = strtotime('+1day');
        $book = array(
            'main_book_id' => 0,
            'user_id' => 1072,
            'room_id' => 1,
            'meeting_topic' => '会议预定测试.',
            'book_start' => date('Y-m-d H:i:s',$tomorrow),
            'book_end' => date('Y-m-d H:i:s',$tomorrow + 3600),
            'memo' => '备注',
            'repeat_type' => 0,
        );

        $response = $this->atom('book/meeting_book_add',$book);
        $response->assertSuccess();
        $id = $response->getData();

        $book['book_start'] = date('Y-m-d H:i:s',$tomorrow-3600);
        $book['book_end'] = date('Y-m-d H:i:s',$tomorrow+1);

        $response = $this->atom('book/meeting_book_add',$book);
        $response->assertFailed();
    }


    public function testUpdate()
    {

    }


    /**
     * 测试删除接口
     * @throws \Frame\Speed\Exception\ApiException
     * @throws \Frame\Speed\Exception\AssertException
     */
    public function testDelete()
    {

    }
}
