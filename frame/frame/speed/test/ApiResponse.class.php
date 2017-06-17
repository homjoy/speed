<?php

namespace Frame\Speed\Test;
use Frame\Speed\Exception\AssertException;


/**
 * Class ApiResponse
 * @package Frame\Speed\Test
 */
class ApiResponse {

    /**
     * api返回的结果
     * @var array
     */
    protected $response = array();

    public function __construct($response)
    {
        $this->response = empty($response) ? array() : $response;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return isset($this->response['data']) ? $this->response['data'] : null;
    }

    /**
     * 接口必须返回正确
     * @throws AssertException
     */
    public function assertSuccess()
    {
        if($this->response['code'] != 200){
            throw new AssertException($this->response);
        }
    }

    /**
     * 接口必须返回错误
     * @throws AssertException
     */
    public function assertFailed()
    {
        if($this->response['code'] == 200){
            throw new AssertException($this->response,'assertFailed失败.');
        }
    }

    /**
     * @param $fieldName
     * @throws AssertException
     */
    public function assertHasField($fieldName)
    {
        if(!isset($this->response['data'][$fieldName])){
            throw new AssertException($this->response,"assertHasField.{$fieldName} not exists.");
        }
    }

    /**
     * @param $count
     * @throws AssertException
     */
    public function assertDataCount($count)
    {
        if(!isset($this->response['data'])){
            throw new AssertException($this->response,"assertDataCount. no data.");
        }

        if(count($this->response['data']) !== $count){
            throw new AssertException($this->response,"assertDataCount {$this->response['data']}!=={$count}");
        }
    }

    /**
     * @param $data
     * @throws AssertException
     */
    public function assertDataEqual($data)
    {
        if(!isset($this->response['data'])){
            throw new AssertException($this->response,'assertDataEqual no data.');
        }

        if(isset($this->response['data']) && is_array($data)){
            //http://stackoverflow.com/questions/5678959/php-check-if-two-arrays-are-equal
            @$ret = (is_array($this->response['data'])
                && is_array($data)
                && array_diff($this->response['data'], $data) === array_diff($data, $this->response['data']));

            if(!$ret){
                throw new AssertException($this->response,'assertDataEqual no data.');
            }
        }

        if($this->response['data'] != $data){
            throw new AssertException($this->response,'assertDataEqual not equal.');
        }
    }

    /**
     * @param $field
     * @param $value
     * @throws AssertException
     */
    public function assertFieldEqual($field,$value)
    {
        if(!isset($this->response['data'][$field])){
            throw new AssertException($this->response,"assertFieldEqual {$field} not exists");
        }

        if(is_array($this->response['data'][$field])){
            throw new AssertException($this->response,"assertFieldEqual {$field} is a array.");
        }

        if($this->response['data'][$field] != $value){
            throw new AssertException($this->response,"assertFieldEqual {$field} != $value");
        }
    }

    /**
     * @param $field
     * @param $value
     * @throws AssertException
     */
    public function assertFieldNotEqual($field,$value)
    {
        if(!isset($this->response['data'][$field])){
            throw new AssertException($this->response,"assertFieldEqual {$field} not exists");
        }

        if(is_array($this->response['data'][$field])){
            throw new AssertException($this->response,"assertFieldEqual {$field} is a array.");
        }

        if($this->response['data'][$field] == $value){
            throw new AssertException($this->response,"assertFieldEqual {$field} != $value");
        }
    }
}
