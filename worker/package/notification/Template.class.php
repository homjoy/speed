<?php
namespace Worker\Package\Notification;
use Worker\Package\Common\BaseQuery;



class Template extends BaseQuery{

    /**
     * @param $pusherId
     * @param $templateId
     * @return string
     */
    public function getPusherTemplate($pusherId,$templateId)
    {
        if(empty($pusherId) || empty($templateId)){
            return '';
        }
        $builder = $this->builder()->where('status',1);
        $builder->where('pusher_id',$pusherId)->where('template_id',$templateId);
        return $builder->first();
    }



    /**
     * @return string
     */
    public static function database()
    {
        return 'worker';
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'template';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'template_id';
    }


    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'template_id'       => 0,
        'pusher_id'       => 0,
        'type'       => 1,
        'identity'       => '',
        'thumb_pic'       => '',
        'title'       => '',
        'content'       => '',
        'status'        => 0,
    );
}