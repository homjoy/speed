<?php
namespace  Apicloud\Package\Common;

/**
 * 封装execl 导出功能
 * by guojiezhu
 * 2015/05/06
 */

include VENDOR_PATH.'/phpexcel/PHPExcel.php';
include VENDOR_PATH.'/phpexcel/PHPExcel/IOFactory.php';
include VENDOR_PATH.'/phpexcel/PHPExcel/Writer/Excel2007.php';

class ExportExcel
{

    protected $config = array (); //配置信息
    protected $objPHPExcel; //objce 对象
    protected $author ; //execl 作者信息
    protected $data; //需要写入execl 的data
    public function __construct ($config, $data = array(),$author = array ())
    {
        $this->config = $config;
        $this->objPHPExcel = new \PHPExcel();
        // Set properties
        $defaultAuthor = array (
            'creator'      => 'Meilishuo  List',
            'LastModified' => 'Meilishuo  List',
            'desc'         => 'System generated automatically'
        );
        $this -> author = $defaultAuthor + $author;
        $this -> data = $data;

        return $this;
    }

    /**
     * 设置excel 的作者信息
     * @return $this
     * @throws \PHPExcel_Exception
     */
    protected function setCreator(){
        $this->objPHPExcel->getProperties ()->setCreator ($this->author[ 'creator' ]);
        $this->objPHPExcel->getProperties ()->setLastModifiedBy ($this->author[ 'LastModified' ]);
        $this->objPHPExcel->getProperties ()->setDescription ($this->author[ 'desc' ]);
        $this->objPHPExcel->createSheet ();
        $this->objPHPExcel->setActiveSheetIndex (0);
        return $this;
    }

    /**
     * 设置excel 的头部的信息
     * @return $this
     */
    protected function setHeader(){
        if ( !empty($this->config ) ) {
            $this->objPHPExcel->getProperties ()->setTitle ($this->config [ 'filename' ]);
            $this->objPHPExcel->getProperties ()->setSubject ($this->config [ 'filename' ]);
            foreach ( $this->config [ 'config' ] as $key => $val ) {

                $this->objPHPExcel->getActiveSheet ()->getColumnDimension ($key)->setWidth ($val[ 'width' ]);
                $this->objPHPExcel->getActiveSheet ()->SetCellValue ($key.'1', $val[ 'title' ]);
            }
        }

        return  $this;
    }


    /**
     * 数据复制
     * @return $this
     * 	$index = 0;
     */
    protected function setBody(){
        if ( empty($this->data) ) {
            return $this;
        }
        $currRow = 2;

        foreach ( $this->data as $key => $value ) {
            foreach ( $this->config[ 'config' ] as $ckey => $cval ) {

                if ( !empty($cval[ 'explicit' ]) ) {
                    $this->objPHPExcel->getActiveSheet ()->setCellValueExplicit ($ckey . $currRow,
                        self::getParamsByKey ($value, $cval[ 'field' ]), 's');
                } else {
                    $this->objPHPExcel->getActiveSheet ()->SetCellValue ($ckey . $currRow,
                        self::getParamsByKey ($value, $cval[ 'field' ]));

                }
                //显示全部数字
                if ( !empty($cval[ 'show' ]) ) {

                    $this->objPHPExcel->getActiveSheet ()->getStyle ($ckey . $currRow)->getNumberFormat ()->setFormatCode ('0');

                }
            }
            $currRow++;
        }
        return $this;

    }


    /**
     * 直接导出
     */

    public function output($path='php://output'){
        $this->setCreator();
        $this->setHeader();
        $this->setBody();
        header ('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header ('Content-Disposition: attachment;filename="' . $this->config[ 'filename' ] . '.xlsx"');
        header ('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter ($this->objPHPExcel, 'Excel2007');
//        $objWriter = new \PHPExcel_Writer_Excel5($this->objPHPExcel);
        if($path == 'php://output'){
            $objWriter->save($path);
        }else{
            $objWriter->save($path);
            readfile($path);
        }
    }


    /**
     *
     * @param array $params
     * @param       $key
     *
     * @return string
     */
    protected function getParamsByKey ($params = array (), $key)
    {
        if ( empty($params) ) {
            return '';
        }
        if (isset($params[$key])) {
            return $params[ $key ];
        }

        return '';
    }


}