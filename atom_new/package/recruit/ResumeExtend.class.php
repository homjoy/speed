<?php
namespace Atom\Package\Recruit;
use Atom\Package\Common\BaseQuery;

/**
 *
 * Class ResumeExtend
 * @package Atom\Package\Resume
 *
 */
class ResumeExtend extends BaseQuery{
    const TYPE_COMMON = 0; //普通信息
    const TYPE_WORK = 1; //工作经验
    const TYPE_PROJECT = 2; //项目经验
    const TYPE_EDUCATION = 3; //教育经历
    const TYPE_TRAIN = 4; //培训经历
    const TYPE_PRACTICE = 5; //实践经验
    const TYPE_SKILL = 6; //技能
    const TYPE_LANGUAGE = 7; //语言能力
    const TYPE_CREDENTIAL = 8; //证书信息
    const TYPE_GALLERY = 9; //相关作品
    const TYPE_OTHER = 10; //其他信息


    public static function tableName()
    {
        return 'resume_extend';
    }
}