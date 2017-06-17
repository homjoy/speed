<?php
namespace Worker\Package\Notification;
use Worker\Package\Common\BaseQuery;

/**
 * 消息通知
 * Class Notification
 * @package Worker\Package\Notification
 *
 */
class Notification extends BaseQuery{

    const CHANNEL_MAIL = 1; //邮件通知
    const CHANNEL_SMS = 2;  //短信通知
    const CHANNEL_IM = 4;   //IM 通知
    const CHANNEL_UNKNOWN = 8; //其他通知方式
    public static $channelMap = array(
        'mail' => Notification::CHANNEL_MAIL,
        'sms' => Notification::CHANNEL_SMS,
        'im' => Notification::CHANNEL_IM,
    );


    const STATUS_NOT_SENT = 0; //未发送
    const STATUS_SENDING = 1;   //发送中
    const STATUS_CANCELED = 2;  //取消发送
    const STATUS_DISCARD = 3;   //消息丢弃
    const STATUS_FAILED = 9;    //发送失败
    const STATUS_SUCCESS = 10;  //发送成功
    const STATUS_DELETED = 99;   //消息删除
    const STATUS_SENSITIVE_WORDS = 4;    //敏感词
    const STATUS_BLACKLIST = 5;//黑名单
    
    const RETRIES = 3;//重试3次

    public static $statusMap = array(
        'not_send' => Notification::STATUS_NOT_SENT,
        'sending' => Notification::STATUS_SENDING,
        'canceled' => Notification::STATUS_CANCELED,
        'discard' => Notification::STATUS_DISCARD,
        'failed' => Notification::STATUS_FAILED,
        'success' => Notification::STATUS_SUCCESS,
        'deleted' => Notification::STATUS_DELETED,
        'sensitive' => Notification::STATUS_SENSITIVE_WORDS ,   //敏感词
        'blacklist' => Notification::STATUS_BLACKLIST,
    );

    /**
     * 根据发送方式生成标志位
     * @param $methods
     * @return int
     */
    public function channels2sign($methods)
    {
        if(empty($methods)){
            return static::CHANNEL_UNKNOWN;
        }

        $methods = is_array($methods) ? $methods : array($methods);
        $sign = 0;
        foreach($methods as $m){
            $sign |= isset(static::$channelMap[$m]) ? static::$channelMap[$m] : 0;
        }

        return $sign;
    }

    /**
     * @param $sign
     * @return array
     */
    public function sign2channels($sign)
    {
        $channels = array();
        foreach(static::$channelMap as $channel => $const)
        {
            if($this->hasChannel($sign,$const)){
                $channels[] = $channel;
            }
        }
        return $channels;
    }


    /**
     * 是否需要通过给定渠道进行通知发送
     * @param $sign
     * @param $channel
     * @return bool
     */
    public function hasChannel($sign,$channel)
    {
        return ($sign & $channel) === $channel;
    }


    /**
     * @param $notify
     * @return array|int|string
     */
    public function push($notify)
    {
        if(empty($notify)){
            return 0;
        }
        $notify = array_intersect_key($notify, static::$fields);
        $notify = array_merge(static::$fields, $notify);
        $notify['status'] = 0;

        //内容序列化
        $notify['content'] = is_array($notify['content']) ? json_encode($notify['content']) : $notify['content'];
        //通知方式转换为标志位保存
        $notify['channel'] = $this->channels2sign($notify['channel']);
        return $this->builder()->insert($notify);
    }

    /**
     * 批量插入.
     * @param $notifies
     * @return array|int|string
     */
    public function pushAll($notifies)
    {
        if(empty($notifies)){
            return 0;
        }

        $formattedData = array();
        foreach($notifies as $notify)
        {
            $notify = array_intersect_key($notify, static::$fields);
            $notify = array_merge(static::$fields, $notify);
            $notify['status'] = 0;
            //内容序列化
            $notify['content'] = is_array($notify['content']) ? json_encode($notify['content']) : $notify['content'];
            //通知方式转换为标志位保存
            $notify['channel'] = $this->channels2sign($notify['channel']);
            $formattedData[] = $notify;
        }

        return $this->builder()->insert($formattedData);
    }

    /**
     * 修改通知内容
     * @param $notify
     * @return $this|int
     */
    public function modify($notify)
    {
        if(empty($notify)){
            return 0;
        }
        $data = array_intersect_key($notify, static::$fields);
        $pk = $data[static::pk()];
        unset($data[static::pk()]);

        $data['content'] = json_encode($data['content']);
        $data['channel'] = $this->channels2sign($data['channel']);

        return $this->builder()->where(static::pk(),$pk)->update($data);
    }


    /**
     * @param int $count
     * @return array
     */
    public function pop($count = 100,$channel='',$order_by='notify_id')
    {
        if(empty($channel)){
            return array();
        }
        $begin_time = date('Y-m-d H:i:s',strtotime('-1 hours'));
        return $this->popBy(array(
            'send_at' => array($begin_time,date('Y-m-d H:i:s')),
            'channel' => $channel,
        ),$count,$order_by);
    }

    /**
     * @param $params
     * @param int $count
     * @return array
     */
    public function popBy($params,$count = 100,$order_by='notify_id')
    {
        if(!isset($params['send_at']) || empty($params['send_at'])){
            $params['send_at'] = array(0,date('Y-m-d H:i:s'));
        }
//        if(!isset($params['channel']) || empty($params['channel'])){
//            $params['channel'] = array(static::CHANNEL_MAIL,static::CHANNEL_SMS);
//        }
        $params['status'] = static::STATUS_NOT_SENT; //只获取未发送的
        $notifications = $this->search($params,0,$count,$order_by);

        $notifyIds = array();
        foreach($notifications as $i=>$notify)
        {
            $notifyIds[] = $notify['notify_id'];
            //解析通知内容
            $notify['content'] = json_decode($notify['content'],true);
            //解析通知方式
            $notify['channel'] = $this->sign2channels(intval($notify['channel']));

            $notifications[$i] = $notify;
        }

        //修改为发送中
        $this->statusChanged($notifyIds,static::STATUS_SENDING);

        return $notifications;
    }

    /**
     * @param $pusherId
     * @param int $count
     * @return mixed
     */
    public function popByPusher($pusherId,$count = 100)
    {
        if(empty($pusherId)){
            return array();
        }
        return $this->popBy(array(
            'pusher_id' => $pusherId,
            'send_at' => array(0,date('Y-m-d H:i:s')),
        ),$count);
    }

    /**
     * @param $pusherId
     * @param $customId
     * @param int $count
     * @return array
     */
    public function popByCustom($pusherId,$customId,$count = 100)
    {
        if(empty($pusherId) || empty($customId)){
            return array();
        }
        return $this->popBy(array(
            'pusher_id' => $pusherId,
            'custom_id' => $customId,
            'send_at' => array(0,date('Y-m-d H:i:s')),
        ),$count);
    }

    /**
     *
     * @param $params
     * @return array
     */
    public function search($params,$offset,$limit,$order ='notify_id')
    {
        if(empty($params)){
            return array();
        }

        $builder = $this->builder();

        if(isset($params['pusher_id']) && !empty($params['pusher_id'])){
            $pusherId = is_array($params['pusher_id']) ? $params['pusher_id'] : array($params['pusher_id']);
            $builder->whereIn('pusher_id',$pusherId);
        }

        if(isset($params['custom_id']) && !empty($params['custom_id'])){
            $customIds = is_array($params['custom_id']) ? $params['custom_id'] : array($params['custom_id']);
            $builder->whereIn('custom_id',$customIds);
        }

        if(isset($params['send_at']) && !empty($params['send_at'])){
            $sendAt = is_array($params['send_at']) ? $params['send_at'] : array(0,$params['send_at']);
            $builder->whereBetween('send_at',$sendAt[0],$sendAt[1]);
        }

        if(isset($params['channel'])){
            if(is_array($params['channel'])){
                $builder->whereIn('channel',$params['channel']);
            }else{
                $builder->where('channel',$params['channel']);
            }
        }

        if(isset($params['status'])){
            $status = is_array($params['status']) ? $params['status'] : array($params['status']);
            $builder->whereIn('status',$status);
        }
        $builder->where('send_times','<',self::RETRIES);

        return $builder->offset($offset)->limit($limit)->orderBy($order)->get();
    }

    /**
     * @param $pusherId
     * @param $customId
     * @param int $version
     * @return array
     */
    public function getPusherData($pusherId,$customId,$version = 0,$status = 0)
    {
        if(empty($pusherId) || empty($customId)){
            return array();
        }
        $builder = $this->builder();
        $builder->where('status',$status);
        $builder->where('pusher_id',$pusherId)->where('custom_id',$customId);
        if($version <= 0){
            //取版本最新的
            $builder->orderBy('version','DESC');
        }else{
            $builder->where('version',$version);
        }

        return $builder->first();
    }

    /**
     * 丢弃通知
     * @param $notifyId
     * @return $this|Notification|int
     */
    public function discard($notifyId)
    {
        return $this->statusChanged($notifyId,static::STATUS_DISCARD);
    }

    /**
     * 删除
     * @param $notifyId
     * @return $this|Notification|int
     */
    public function remove($notifyId)
    {
        return $this->statusChanged($notifyId,static::STATUS_DELETED);
    }

    /**
     * 通知结束
     * @param $notifyId
     * @param bool $success
     * @return $this|int
     */
    public function finish($notifyId,$success = true)
    {
        if(empty($notifyId)){
            return 0;
        }

        $params = array();
        $params['status'] = $success ? static::STATUS_SUCCESS : static::STATUS_FAILED ;
        $params['send_at'] = date('Y-m-d H:i:s');

        return $this->builder()->where(static::pk(),$notifyId)->update($params);
    }

    /**
     * 状态变更
     * @param $notifyId
     * @param $status
     * @param $send_times 发送次数
     * @return $this|int
     */
    public function statusChanged($notifyId,$status,$send_times=0)
    {
        if(empty($notifyId)){
            return 0;
        }

        $params = array(
            'status' => $status,
        );
        if($send_times >0){
           $params['send_times'] = $send_times;
        }
        $ids = is_array($notifyId) ? $notifyId : array($notifyId);
        return $this->builder()->whereIn(static::pk(),$ids)->update($params);
    }

    /**
     * 自定义删除
     * @param $params
     * @return $this|int
     */
    public function deleteByCustom($params)
    {
        if(empty($params) || empty($params['pusher_id']) || empty($params['custom_id'])){
            return 0;
        }

        $status = array(
            'status' => static::STATUS_DELETED,
        );


        $builder = $this->builder()->where('pusher_id',$params['pusher_id'])
            ->where('custom_id',$params['custom_id']);

        //只删除未发送的.不绑定PDO，防止update 的status 和where 里面的status 冲突。
        $builder->where($builder->raw("status = 0"));

        if(!empty($params['to_id'])){
            $toId = is_array($params['to_id']) ? $params['to_id'] : array($params['to_id']);
            $builder->whereIn('to_id',$toId);
        }
//        var_dump($builder->count());exit;
        return $builder->update($status);
    }

    /**
     * 修改通知内容
     * @param $notify
     * @return $this|int
     */
    public  function updateAfterContent($notify)
    {
        if(empty($notify)){
            return 0;
        }
        $data = array_intersect_key($notify, static::$fields);
        $pk = $data[static::pk()];
        unset($data[static::pk()]);
        $data['after_content'] = $data['after_content'];

        return $this->builder()->where(static::pk(),$pk)->update($data);
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
        return 'notifications';
    }

    /**
     * @return string
     */
    public static function pk()
    {
        return 'notify_id';
    }


    /**
     * 字段列表
     * @var array
     */
    public static $fields = array(
        'notify_id'       => 0,
        'pusher_id'       => 0,
        'custom_id'       => 0,
        'custom_version'       => 0,
        'title'       => '',
        'content'       => '',
        'template_id'       => 0,
        'channel'       => 0,
        'from_id'       => 0,
        'to_id'       => 0,
        'mail'       => '',
        'phone'       => '',
        'send_at'       => '0000-00-00 00:00:00',
        'status'        => 0,
        'weights'       => 100,
        'send_times'    =>0,
        'after_content' => ''
    );
}