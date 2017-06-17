<?php
namespace Atom\Package\Migrate;

use Atom\Package\Routine\BookHasServices;
use Atom\Package\Routine\BookHasUsers;
use Atom\Package\Routine\BookInfo;
use Atom\Package\Routine\BookInfoVersion;
use Atom\Package\Routine\EventReply;
use Libs\Util\ArrayUtilities;

/**
 * Class BookMigration
 * @package Atom\Package\Migrate
 */
class BookMigration
{
    /**
     * @throws \Exception
     */
    public static function meeting()
    {
        $count = 0;
        $max = Crab::model()->booksCount();
//        $max = 100;
        do {
            $books = Crab::model()->getMeetingBooks($count, 30);

            foreach ($books as $b) {
                //有分会场？视频会议.
                $notices = empty($b['notice']) ? array() : explode(',', $b['notice']);
                //如果有分会场，则为视频会议.
                //否则如果开启了电话设备，则为电话会议.
                $meetingType = !empty($b['branch_field']) ? BookInfo::TYPE_VIDEO_CAMERA
                    : (in_array(2, $notices) ? BookInfo::TYPE_PHONE : BookInfo::TYPE_NORMAL);

                //重复类型.
                switch ($b['is_repeat']) {
                    case 'day' :
                        $repeatType = BookInfo::REPEAT_DAY;
                        break;
                    case 'week' :
                        $repeatType = BookInfo::REPEAT_WEEK;
                        break;
                    case 'month' :
                        $repeatType = BookInfo::REPEAT_MONTH;
                        break;
                    default:
                        $repeatType = BookInfo::REPEAT_NO;
                }


                $startTime = strtotime($b['start_time']);
                $endTime = strtotime($b['end_time']);
                $mainBook = Crab::model()->getMainBook($b['book_id']);

                //不是多会场或者本身就是主会场.
                if(empty($mainBook) || $mainBook['relation_book_id'] == $mainBook['book_id']){
                    $mainBookId = 0;
                }else{
                    $mainBookId = $mainBook['book_id'];
                }
                $newBook = array(
                    'book_id' => $b['book_id'],
                    'main_book_id' => $mainBookId,
                    'user_id' => $b['user_id'],
                    'room_id' => $b['room_id'],
                    'meeting_type' => $meetingType,
                    'meeting_topic' => $b['meeting_topic'],
                    'book_start' => $b['start_time'],
                    'book_end' => $b['end_time'],
                    'book_date' => date('Y-m-d', $startTime),
                    'time_start' => date('H:i:s', $startTime),
                    'time_end' => date('H:i:s', $endTime),
                    'month' => date("m", $startTime),
                    'month_day' => $b['book_date'],
                    'week_day' => $b['book_week'],
                    'timezone' => 'Asia/Shanghai',
                    'memo' => $b['others'],
                    'repeat_type' => $repeatType,
                    'status' => $b['status'],
                    'create_time' => $b['create_time'],
                    'version' => 1,
                );

                BookInfo::model()->insert($newBook);

                $bookHasUsers = Crab::model()->bookHasUsers($b['book_id']);
                foreach ($bookHasUsers as $u) {
                    $ret = BookHasUsers::model()->insert(array(
                        'id' => $u['map_id'],
                        'book_id' => $u['book_id'],
                        'room_id' => $b['room_id'],
                        'user_id' => $u['user_id'],
                        'status' => $u['status'],
                    ));
                }

                if(!empty($b['notice'])) {
                    $notices = explode(',',$b['notice']);
                    $servicesMap = array('1'=>3,'2'=>5,'3'=>1);
                    foreach($notices as $val){
                        BookHasServices::model()->insert(array(
                            'book_id' => $b['book_id'],
                            'service_id' => $servicesMap[$val], //待客服务
                            'memo' => '',
                            'status' => $b['status'],
                        ));
                    }
                }

                if ($b['provide_service']) {
                    BookHasServices::model()->insert(array(
                        'book_id' => $b['book_id'],
                        'service_id' => 7, //待客服务
                        'memo' => '',
                        'status' => $b['status'],
                    ));
                }
            }

            $count += 30;
        } while ($count < $max);
    }

    public static function meetingRelation()
    {
        $max = Crab::model()->relationBookCount();
        $count = 0;
        do {
            $relationBooks = Crab::model()->relationBook($count, 30);

            foreach ($relationBooks as $branch) {
                //主会场.
                if ($branch['book_id'] == $branch['relation_book_id']) {
                    continue;
                } else {
                    BookInfo::model()->updateMainBookId($branch['relation_book_id'], $branch['book_id']);
                }
            }

            $count += 30;
        } while ($count < $max);
    }

    public static function version()
    {

        $max = BookInfo::model()->repeatBooksCount();
        $count = 0;
        do {
            $repeatBooks = BookInfo::model()->repeatBooks($count, 30);

            foreach ($repeatBooks as $book) {
                $version = $book;
                $version['version'] = 1;//
                $bookHasUser = BookHasUsers::model()->getBookUserIds($book['book_id']);
                $bookHasUser = isset($bookHasUser[$book['book_id']]) ? $bookHasUser[$book['book_id']] : array();
                $version['user_id_json'] = json_encode($bookHasUser);
                $bookHasService = BookHasServices::model()->getBookServices($book['book_id']);
                $currentServiceIds = ArrayUtilities::my_array_column($bookHasService,'service_id');
                $version['service_id_json'] = json_encode($currentServiceIds);
                BookInfoVersion::model()->changed('add',$version);
            }
            $count += 30;
        } while ($count < $max);
    }

    /**
     * 拒绝记录.
     * @throws \Exception
     */
    public static function cancel()
    {
        $books = Crab::model()->cancelBooks();
        if(empty($books)){
            return ;
        }


        foreach($books as $b)
        {
            EventReply::model()->insert(array(
                'reply_id'       => $b['id'],
                'book_id'       => $b['book_id'],
                'user_id'       => $b['user_id'],
                'reply'       => EventReply::REPLY_DECLINE,
                'reason'          => $b['cancel_desc'],
                'status'        => 1,
            ));
        }
    }
}