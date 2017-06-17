<?php
namespace Libs\Util;

class DBHuodongCommonToolHelper extends \Libs\DB\DBConnManager {
    const _DATABASE_ = 'dolphin';
}

class HuodongCommonTool {
    private static $instance;

    private $contentTable = 't_dolphin_content_data';
    private $col = 'content_id, content_title, data_json, data_public, start_time, end_time, remark, time';

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {

    }   

    /**
     * 获取数据
     * @param string     $id 
     * @param int        $content_id
     * @return array
     */
    public function getContentData($id, $content_id = 0, $offset = 0, $limit = 10) {
        $sqlComm = "SELECT {$this->col} FROM {$this->contentTable} 
                    WHERE password = :id AND is_active = 1";

        $sqlData['id'] = $id;
        if (!empty($content_id)) {
            $sqlComm .= " AND content_id = :_content_id";
            $sqlData['_content_id'] = $content_id;
        }
        $sqlComm .= " ORDER BY start_time DESC";
        $sqlComm .= " LIMIT $offset, $limit";

        $result = array();
        $result = DBHuodongCommonToolHelper::getConn()->read($sqlComm, $sqlData);
        return $result;
    }

    /**
     * 获取数据总数
     * @param string     $id 
     * @return array
     */
    public function getTotalNum($id) {
        $sqlComm = "SELECT count(*) as num FROM {$this->contentTable} 
                    WHERE password = :id AND is_active = 1";

        $sqlData['id'] = $id;

        $result = array();
        $result = DBHuodongCommonToolHelper::getConn()->read($sqlComm, $sqlData);
        return $result[0]['num'];
    }
}
