<?php

namespace Atom\Scripts;

use Atom\Package\Migrate\BookMigration;
use Atom\Package\Migrate\MeetingRoomMigration;

class Migrate extends \Frame\Script {

    public function run() {

        try{
//            MeetingRoomMigration::city();
//            MeetingRoomMigration::company();
//            MeetingRoomMigration::newRooms();
            BookMigration::meeting();
            //插入的时候直接查询主会场关联.
            //BookMigration::meetingRelation();
            BookMigration::version();
            BookMigration::cancel();
        }catch (\Exception $e){
            var_dump($e->getMessage());
        }

        $this->app->response->setBody('数据迁移完毕.');
    }
}
