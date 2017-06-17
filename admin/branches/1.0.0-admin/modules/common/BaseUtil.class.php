<?php
namespace Admin\Modules\Common;

class BaseUtil
{

    public static function getGenTree($data, $pid = 0)
    {

        if (empty($data)) {
            return false;
        }

        $tree = array();
        foreach ($data as $k => $v) {

            if ($v['parent_id'] == $pid) {

                //父亲找到儿子
                $v['child_info'] = self::getGenTree($data, $v['tree_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    public static function getParentTree($all, $data)
    {

        //统一全部Tree格式
        foreach ($all as $k => $v) {

            if (isset($v['parent_info'])) {
                unset($v['parent_info']);
                $all[$k] = $v;
            }
        }

        $tree = array();
        foreach ($data as $k => $v) {

            $tree[$v['tree_id']] = $all[$v['tree_id']];

            //如果当前节点的父亲节点不为0，且其父亲节点不在数组中
            if ($v['parent_id'] != 0 && !isset($data[$v['parent_id']])) {

                $tree[$v['parent_id']] = $all[$v['parent_id']];
            }
        }

        return $tree;
    }

    /**
     * 获取url 对应的父亲数据
     * @param $data
     * @return array()
     */
    public static function getParentUrlTree($data)
    {
        if (empty($data)) {
            return false;
        }
        $tree = array();
        foreach ($data as $k => $v) {
            if($v['tree_url'] == '/') {
                $tree[$v['tree_id']] = $v['tree_name'];
            }else{
                $tree = $tree + self::getParentInfo($data, $v);
            }

        }

        return $tree;
    }


    public static function getParentInfo($data,$v){
        if(empty($v['parent_id'])){
            return array(
                'tree_name' =>  $v['tree_name'],
                'tree_url' => $v['tree_url']
            );
        }
        $tree[$v['tree_url']]['tree_url'] =  $v['tree_url'];
        $tree[$v['tree_url']]['tree_name'] =  $v['tree_name'];
        $tree[$v['tree_url']]['parent'] = self::getParentInfo($data,$data[$v['parent_id']]);
        return $tree;
    }

    //解析生成面包屑
    public static function getBreadCrumbs($data,$path){
        if(empty($data[$path])){
            return array();
        }
        $new_array = array();
        $path_url_array = $data[$path];
        $i = 0;
        do{

            if(empty($path_url_array)) break;
            $tmp_array = array(
                'url' => $path_url_array['tree_url'],
                'tree_name'=> $path_url_array['tree_name']
            );
            array_unshift($new_array,$tmp_array);
            if(!empty($path_url_array['parent'])){
                $current_path = current( $path_url_array['parent']);
                if(isset($current_path['parent'])){
                    $path_url_array = $current_path;
                }else{
                    $path_url_array = $path_url_array['parent'];
                }
            }else{
                break;
            }
            $i ++;
        }while($i<10);

        return $new_array;
    }

}