<?php
namespace Worker\Package\Utils;

/**
 * Class RegexMatcher
 * @package Worker\Package\Common
 */
class RegexMatcher{
    /**
     * @var string
     */
    protected $content = '';

    protected $matches = array();

    /**
     * @param $content
     * @param bool $cleanup 是否对内容进行清理
     * @throws \Exception
     */
    public function __construct($content,$cleanup=true)
    {
        if(empty($content)){
            throw new \Exception('RegexMatcher 内容不能为空!');
        }

        //去掉换行、制表等特殊字符
        $this->content = $cleanup ? preg_replace("/[\t\n\r]+/","",$content) : $content;
    }

    /**
     * 正则提取单条数据，可以包含多个命名分组
     * @param $regex
     * @param array $groups
     * @return string
     */
    public function one($regex,$groups = array()){
        if (!preg_match($regex, $this->content, $this->matches)) {
            return '';
        }

        if(empty($this->matches) || empty($this->matches[1])){
            return '';
        }

        $groups = !empty($groups) ? $groups : $this->matchesGroups();

        //依然没有分组，只提取单个数据
        if(empty($groups)){
            return rtrim(trim($this->matches[1]));
        }
        if(count($groups) == 1){
            return rtrim(trim($this->matches[$groups[0]]));
        }

        //多个数据
        $result = array();
        foreach($groups as $group){
            if(isset($this->matches[$group])){
                $result[$group] = rtrim(trim($this->matches[$group]));
            }
        }
        return $result;
    }


    /**
     * 正则匹配全部数据，自动提取命名分组里面的数据
     * @param $regex
     * @param array $groups
     * @return array
     */
    public function all($regex,$groups = array())
    {
        if(!preg_match_all($regex, $this->content, $this->matches) || empty($this->matches)){
            return array();
        }

        $results = array();
        $groups = !empty($groups) ? $groups : $this->matchesGroups();

        for($i = 0,$count = $this->count(); $i<$count; $i++){
            if(empty($groups)){
                $results[] = $this->matches[1][$i];
            }else{
                $data = array();
                foreach($groups as $group){
                    if(isset($this->matches[$group]) && isset($this->matches[$group][$i])){
                        $data[$group] = rtrim(trim($this->matches[$group][$i]));
                    }else{
                        $data[$group] = '';
                    }
                }
                $results[] = $data;
            }
        }
        if(empty($results)){return array();}
        return $results;
        //return count($results) == 1 ? $results[0]: $results;
    }


    /**
     * 多步正则提取
     * 目前除了最后一步只支持多次单步提取
     * @param array $rules like array(array('one','/regex here/m'),array('all','/regex here/m'))
     * @return array|string
     * @throws \Exception
     */
    public function multi(array $rules)
    {
        if(empty($rules)){
            return '';
        }

        $lastHtml = $this->content;
        $finalStep = count($rules)-1;

        $result = '';
        foreach($rules as $i => $rule){
            if(!isset($rule[0]) || !isset($rule[1])){
                throw new \Exception("RegexMatcher->multi rule format is error! should be (type,regex)");
            }
            switch($rule[0]){
                case 'one': $result = $this->one($rule[1]); break;
                case 'all': $result = $this->all($rule[1]); break;
                default:
                    throw new \Exception("RegexMatcher->multi not support type:{$rule[0]}");
            }

            if($i !== $finalStep){
                if(is_array($result)){
                    throw new \Exception("RegexMatcher->multi not support multi result during step");
                }
                $this->content = (string)$result;
            }
        }

        //多步提取之后还原
        $this->content = $lastHtml;
        return $result;
    }

    /**
     * 匹配结果数
     * @return int
     */
    public function count()
    {
        return isset($this->matches[0]) ? count($this->matches[0]) : 0;
    }

    /**
     * 提取匹配结果里面的命名分组
     * @return array
     */
    protected function matchesGroups()
    {
        $groups = array();
        foreach($this->matches as $key=>$m){
            //有命名的分组
            if(is_string($key)){
                $groups[] = $key;
            }
        }
        return array_unique($groups);
    }
}