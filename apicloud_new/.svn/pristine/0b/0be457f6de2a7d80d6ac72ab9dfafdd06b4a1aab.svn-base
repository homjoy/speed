<?php
namespace Apicloud\Package\Common;

class Page {

    /**
     * 分页
     * @param array $data       数据
     * @param int   $total      总条数
     * @param int   $page       页数
     * @param int   $page_size  每页条数
     * @return array
     */
    public static function page($data,$total,$page = 1,$page_size = 10){
        $maxPage = !empty($total) ? ceil($total/$page_size) : 1;  //一共多少页

        $datas = array(
            'code'  => '200',
            'msg'   => '',
            'page'  => array(
                'currentPage'   => $page,
                'maxPage'       => $maxPage,
                'pageSize'      => $page_size,
            ),
            'data' => $data,
        );

        return $datas;
    }

}
