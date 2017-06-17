<?php
namespace Frame\Speed\Http;

use Frame\Speed\Exception\ParameterException;

/**
 * 参数过滤对象~
 * Class ParameterFilter
 * @package Atom\Package\Common
 */
class ParameterFilter
{
    /**
     * 参数数据
     * @var array
     */
    protected $parameters = array();

    /**
     * 参数格式规则
     * @var array
     */
    protected $rules = array();

    /**
     * 过滤后的数据
     * @var array
     */
    protected $formatData = array();

    /**
     * 错误信息
     * @var array
     */
    protected $errors = array();

    /**
     * 是否强制抛出异常
     * @var bool
     */
    protected $force = false;

    /**
     * @param array $data
     * @param array $rules
     * @throws \Exception
     */
    public function __construct(array $data, $rules = array(),$force = false)
    {
        $this->force = $force;
        $this->filter($data,$rules);
    }

    /**
     * 数据过滤
     * @param array $data
     * @param array $rules
     * @throws ParameterException
     * @throws \Exception
     */
    public function filter(array $data,array $rules = array())
    {
        foreach ($data as $name => $value) {
            $this->parameters[$name] = $value;
        }
        $this->rules = $rules;
        $this->formatData = $this->permit($rules);
        if($this->force && $this->hasError()){
            throw new ParameterException('参数错误！',10004,$this->getErrors());
        }
    }

    /**
     * 检测特定数据
     * @param $data
     * @param $rules
     * @return array
     * @throws \Exception
     */
    public function permitData($data,$rules)
    {
        if (empty($rules)) {
            return array(array(),array());
        }
        $originParameters = $this->parameters;
        $originErrors = $this->errors;
        $this->parameters = $data;
        $this->errors = array();

        $parameters = $this->permit($rules);
        $errors = $this->errors;

        $this->parameters = $originParameters;
        $this->errors = $originErrors;

        if($this->force && $this->hasError()){
            throw new ParameterException('参数错误！',10004,$this->getErrors());
        }

        return array($parameters,$errors);
    }

    /**
     * 获取过滤后的数据
     * @param null $name
     * @param string $type
     * @param null $default
     * @return array|float|int|null|string
     */
    public function safe($name = null, $type = 'string', $default = null)
    {
        if (is_null($name)) {
            return !empty($this->formatData) ? $this->formatData : $this->parameters;
        }
        if (isset($this->formatData[$name])) {
            return $this->formatData[$name];
        }

        $type = $type === 'array' ? 'getArray' : $type;
        //获取值并转换为相应的类型
        if(!method_exists($this,$type)){
            return isset($this->parameters[$name]) ? $this->parameters[$name]: $default;
        }
        return call_user_func(array($this,$type),$name, $default);
    }

    /**
     * 根据规则检测参数
     * @param $rules
     * @return array
     * @throws \Exception
     */
    protected function permit($rules)
    {
        if (empty($rules)) {
            return array();
        }

        $parameters = array();
        $errors = array();

        foreach ($rules as $name => $rule) {
            //没有提供参数
            if (!isset($this->parameters[$name])) {
                //使用默认值
                $parameters[$name] = isset($rule['default']) ? $rule['default'] : self::defaultValue($rule['type']);

                //参数必须的情况下记录错误
                if (isset($rule['required']) && $rule['required']) {
                    //必须的参数没有提供
                    //提供默认值，或者可以统一为null
                    $this->errors[$name][] = '参数必须!';
                }
                continue;
            }

            //特殊处理multiId
            if($rule['type'] === 'multiId'){
                $parameters[$name] = $this->multiId($name,isset($rule['default']) ? $rule['default'] : null);
                continue;
            }
            if(isset($this->parameters[$name]) && is_array($this->parameters[$name])){
                //如果设置了数组的规则
                if(isset($rule['rules'])){
                    //TODO 根据规则检测数组元素
                }else{
                    //没有规则的直接跳过检测
                    $parameters[$name] = $this->parameters[$name];
                }
                continue;
            }

            //如果有该参数，但是为空串，则看是否允许为空
            //默认允许空参数
            if (!is_string($this->parameters[$name]) || strlen($this->parameters[$name]) === 0){
                //如果需要默认为不允许非空，则改为如下条件
                //(isset($rule['allowEmpty']) && $rule['allowEmpty'] === true)){
                //空串的情况下，如果提供了默认值，则赋值为默认值
                if(!isset($rule['allowEmpty']) || $rule['allowEmpty'] === true){
                    $parameters[$name] = isset($rule['default']) ? $rule['default'] : self::defaultValue($rule['type']);
                    //没有值，就不继续格式检查了..
                }else{
                    $this->errors[$name][] = "{$name}不允许为空.";
                }
                continue;
            }

            //获取值并转换为相应的类型
            if(!method_exists($this,$rule['type'])){
                throw new \Exception('无效的参数类型.');
            }
            $value = call_user_func(array($this,$rule['type']),$name);
            $parameters[$name] = $value;

            $errors = $this->checkValueByRule($value, $rule);
            if (count($errors)) {
                if(isset($this->errors[$name])){
                    $this->errors[$name] = array_merge($this->errors[$name],$errors);
                }else{
                    $this->errors[$name] = $errors;
                }
            }
        }

        return $parameters;
    }

    /**
     * 参数是否有错误
     * @return bool
     */
    public function hasError()
    {
        return (count($this->errors) > 0 ? true : false);
    }

    /**
     * 获取参数的错误信息
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * 增加错误
     * @param $name
     * @param $errors
     * @return $this
     */
    public function addError($name,$errors)
    {
        if(!isset($this->errors[$name])){
            $this->errors[$name] = is_array($errors) ? $errors : array($errors);
        }else{
            $this->errors[$name] = array_merge($this->errors[$name],$errors);
        }
        return $this;
    }


    /**
     * 获取各种类型的默认值
     * @param string $type
     * @return array|float|int|string
     */
    protected static function defaultValue($type = 'string')
    {
        switch ($type) {
            case 'string':
                return '';
            case 'integer':
                return 0;
            case 'float':
                return 0.0;
            case 'array':
                return array();
            default:
                return null;
        }
    }

    /**
     * 根据规则检测参数值是否正确
     * @param $value
     * @param array $rule
     * @return array
     * @throws \Exception
     */
    protected function checkValueByRule($value, array $rule = array())
    {
        $errors = array();
        if(empty($rule)){
            return $errors;
        }

        //检测每一种规则
        foreach ($rule as $name => $right) {
            switch ($name) {
                case 'type':
                case 'default':
                case 'required':
                case 'allowEmpty':
                    if($right == false && $value === ''){
                        $errors[] = "数据为空.";
                    }
                    break;
                case 'min':
                    if ($value < $right) {
                        $errors[] = "最小值为{$right}";
                    }
                    break;
                case 'max':
                    if ($value > $right) {
                        $errors[] = "最大值为{$right}";
                    }
                    break;
                case 'minLength':
                    if (mb_strlen($value, 'UTF-8') < $right) {
                        $errors[] = "长度不能少于{$right}位.";
                    }
                    break;
                case 'maxLength':
                    if (mb_strlen($value, 'UTF-8') > $right) {
                        $errors[] = "长度不能超过{$right}位.";
                    }
                    break;
                case 'enum':
                    if (!is_array($right)) {
                        throw new \Exception('enum data should be valid array');
                    }
                    if (!in_array($value, $right)) {
                        $errors[] = "必须是以下几种(" . implode(",", $right) . ')';
                    }
                    break;
                case 'regex':
                    if($right && $right[0] !== '/'){
                        $right = '/'.$right.'/';
                    }
                    if(!preg_match($right,$value,$matches) > 0){
                        $errors[] = "格式应该为: {$right}";
                    }
                    break;
                case 'callback':
                    if (!is_callable($right)) {
                        throw new \Exception("{$right} is not a valid callback");
                    }
                    if (!call_user_func($right, $value, $this )) {
                        $errors[] = "无效数据.";
                    }
                    break;
                case 'phone':
                    if (!preg_match('/^1[34578][0-9]{9}$/',$value)) {
                        $errors[] = "手机号格式不正确.";
                    }break;
                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                        $errors[] = "链接格式不正确.";
                    }break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "邮箱格式不正确.";
                    }break;
                default:
                    throw new \Exception("Not Support {$name}");
                    break;
            }
        }

        return $errors;
    }

    /**
     * 过滤字符串
     * @param $name string
     * @param $default string
     * @return string
     */
    public function string($name, $default = null)
    {
        $result = null;
        if (isset($this->parameters[$name])) {
            $result = $this->parameters[$name];
            $result = trim($result);
            $result = urldecode($result);
            //从\Libs\Http\BasicRequest渠道的数据已经被转义过了,防止重复转义.
//            $result = htmlspecialchars($result);
            $result = (string)$result;
        } elseif (!is_null($default)) {
            $result = $default;
        }
        return $result;
    }

    /**
     * 过滤整型
     * @param $name
     * @param null $default
     * @return int|null
     */
    public function integer($name, $default = null)
    {
        $result = null;
        if (isset($this->parameters[$name])) {
            $result = $this->parameters[$name];
            $result = trim($result);
            $result = intval($result);
        } elseif (!is_null($default)) {
            $result = $default;
        }
        return $result;
    }

    /**
     * 过滤浮点类型
     * @param $name
     * @param null $default
     * @return float|null|string
     */
    public function float($name, $default = null)
    {
        $result = null;
        if (isset($this->parameters[$name])) {
            $result = $this->parameters[$name];
            $result = trim($result);
            $result = floatval($result);
        } elseif (!is_null($default)) {
            $result = $default;
        }
        return $result;
    }

    /**
     * 过滤布尔值
     * @param $name
     * @param bool $default
     * @return bool
     */
    public function boolean($name, $default = false)
    {
        if (isset($this->parameters[$name])) {
            return boolval($this->parameters[$name]);
        }else{
            return $default;
        }
    }

    /**
     * 检查字段是否存在，可选检查是否为空
     * @param $name
     * @param bool $checkEmpty
     * @return bool
     */
    public function exists($name, $checkEmpty = false)
    {
        if ($checkEmpty) {
            $result = isset($this->parameters[$name]) &&
                (is_string($this->parameters[$name]) && strlen($this->parameters[$name]) > 0);
        } else {
            $result = isset($this->parameters[$name]);
        }
        return $result;
    }

    /**
     * 获取原始的参数数组
     * @return array
     */
    public function toArray()
    {
        return $this->parameters;
    }

    /**
     * @param $name
     * @param array $default
     * @return array
     */
    public function getArray($name,$default = array())
    {
        $result = array();
        if (isset($this->parameters[$name])) {
            $result = (array)$this->parameters[$name];
        } elseif (!is_null($default)) {
            $result = $default;
        }
        return $result;
    }

    /**
     * 取单个或者多个ID
     * @param $name
     * @param array $default
     * @return array
     */
    public function multiId($name,$default = null)
    {
        $ids = array();
        if (!isset($this->parameters[$name]) || $this->parameters[$name] === '') {
            return is_null($default) ? $ids : $default;
        }

        $result = $this->parameters[$name];
        if(is_array($result)){
            return array_map('intval',$result);
        }
        if(is_string($result)){
            return array_map('intval',explode(',',$result));
        }

        return intval($result);
    }

    /**
     * 取日期值，可以指定格式
     * @param $name
     * @param string $format
     * @return bool|string
     */
    public function datetime($name, $format = 'Y-m-d H:i:s',$default = null)
    {
        $value = $this->string($name,$default);
        if(empty($value)){
            return $default;
        }

        $datetime = strtotime($value);
        return date($format,$datetime);
    }

    /**
     * @param bool $force
     * @return $this
     */
    public function forceException($force)
    {
        $this->force = $force ? true : false;
        return $this;
    }

    /**
     * 使用自己的回调进行额外的参数检测
     * @param $cb
     * @return mixed
     * @throws ParameterException
     * @throws \Exception
     */
    public function callback($cb)
    {
        if(!is_callable($cb)){
            throw new \Exception("{$cb}不是一个有效的回调函数");
        }

        $result = call_user_func($cb,$this);

        if(!$result && $this->force){
            throw new ParameterException('参数错误！',10004,$this->getErrors());
        }
        return $result;
    }

	/**
	 * 过滤查询参数
     * @param $params
	 * @return array
	 */
	public function params_filter($params)
	{
		if(!empty($params)){
			foreach($params as $k => $v){
				if (empty($v)) {
					unset($params[$k]);
				}
			}
		}
        unset($params['page']);
        unset($params['page_size']);
		return $params;
	}

}
