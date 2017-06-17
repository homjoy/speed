<?php

namespace Libs\Serviceclient\Api;

/**
 * 记录api的请求方式和服务模块
 * @author zx
 */
class AtomApiList extends \Libs\Serviceclient\Api\ApiList {

    protected static $apiList = array(
        //测试

        'a/b' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'staff/get_base_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
    
    
        //用户
        'user/login_info_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/login_info_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/login_info_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/login_info_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    		
    	
        'user/user_info_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //用户信息
        'user/user_info_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_info_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_info_get_by_mail' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_info_search' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_info_get_by_params' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_avatar_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //头像
        'user/user_avatar_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_avatar_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_avatar_bget' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_avatar_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_pinfo_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //个人信息
        'user/user_pinfo_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_pinfo_bget' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'user/user_personal_info_search' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_personal_info_birthday' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_work_info_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //工作信息
        'user/user_work_info_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'user/user_work_info_bget' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    	'user/user_job_level_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //职位级别
    	'user/user_job_role_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //职位角色
    	'user/user_job_title_bget' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //所有职位title
    	'user/user_job_title_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //所有职位title
    	'user/user_info_update_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

    	//部门
        'department/dept_info_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/dept_info_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/dept_info_bget' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_info_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_leader_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/dept_leader_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/dept_leader_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_info_all' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_id_by_user_id' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/get_all_depart_leader' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_leader_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/update_dept_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/update_depart_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/get_direct_leader' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/get_depart_leader' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //获取部门领导领导
        //弃用接口
        'department/dept_info_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/update_dept_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/create_dept_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //新部门zhouhong
        'department/depart_info_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //部门信息批量查询
        'department/update_depart_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新部门信息
        'department/create_depart_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建部门
        'department/update_depart_sub' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//部门LEADER信息更改
        'department/create_depart_sub' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/depart_sub_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/dept_relation_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        'department/get_dept_relation' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/get_depart_relation' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/depart_relation_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //部门临时表数据
        'department/depart_sub_temp_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/create_depart_sub_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/depart_relation_temp_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/create_depart_relation_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/depart_info_temp_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'department/create_depart_info_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/backup_all_depart_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/create_all_departInfo_by_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/update_depart_relation_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/update_depart_sub_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/update_depart_info_temp' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'department/hacked_depart_info_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 6)),

        //通讯录
        'contacts/ajax_search' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'contacts/search' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    		

        //公司
        'company/city_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/city_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/city_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'company/city_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'company/company_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/company_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/company_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'company/company_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'company/office_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/office_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'company/office_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'company/office_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //会议室
        'meeting/meeting_room_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/meeting_room_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/meeting_room_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/meeting_room_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/equipment_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/equipment_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/equipment_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/equipment_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/equipment_relation_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/equipment_relation_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/equipment_relation_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/equipment_relation_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/meeting_room_service_all' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'meeting/room_service_all' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //服务
        'meeting/room_service_add' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //会议室服务规则
        'meeting/room_service_rule_add' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_rule_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_rule_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'meeting/room_service_rule_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

		//个人时间
        'routine/user_time_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_time_list_count' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_time_check' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_time_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_share_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_share_check' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_share_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_share_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_share_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_reject' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_update_time' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_relation_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/user_time_relation_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_time_relation_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'routine/user_time_relation_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'routine/get_meeting_users_list' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)), //haibinzhou 批量获取参会人id
		//菜单
        'core/menu_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //修改邮箱密码
        'core/get_mail_password_time' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/update_mail_password_time' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/add_mail_password_time' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'core/get_user_register' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)), //haibinzhou 获取用户注册信息


        //帐号注册登记
        'core/mls_account_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/get_user_account' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //itserver  wifi vpn redmine 
        'itserver/visitor_info_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'itserver/visitor_wifi_disable' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/visitor_wifi_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/visitor_wifi_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/create_official_mail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/update_official_mail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/get_official_mail' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //token
        'core/user_token_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/user_token_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //配置相关
        'core/config_get_child' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/config_get_value' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/config_search' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/config_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'core/config_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/config_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'core/config_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

		//数据备份
        'core/create_data_backup' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/data_backup_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //会议室预定
        'book/meeting_book_add' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/meeting_book_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/meeting_update_time' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/invite_reply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/meeting_book_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/meeting_zones_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/get_room_books' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'book/get_books' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'book/get_book_users' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/get_conflict_books' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'book/get_user_books' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'book/get_book_notice_service' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'book/get_book_notice_user' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'book/book_sign_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'book/book_sign_delete' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'book/book_sign_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'book/book_sign_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'book/book_sign_confirm' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'book/get_room_book_service' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//haibinzhou 获取会议室服务
        'book/get_refuse_reply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//haibinzhou 获取拒绝会议的小伙伴
        'book/get_sign_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//haibinzhou 得到会议签到信息
        'book/check_room_book' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//haibinzhou 根据指定时间获取没被预定的会议室

        //获取邮件组相关信息
        'mail/mail_group_search' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'mail/mail_group_user_search' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'mail/mail_group_user_create' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//guojiezhu 创建邮箱用户
        'mail/mail_group_user_update' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//guojiezhu 更新邮箱用户
        'mail/mail_group_update' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//guojiezhu 更新邮箱用户
        'mail/mail_group_create' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//guojiezhu 创建邮箱
        'mail/mail_group_user_delete' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//guojiezhu 删除组下的用户

		'mail/create_mail_group_depart_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//guojiezhu
		'mail/get_mail_group_depart_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//guojiezhu
		'mail/update_mail_group_depart_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//guojiezhu
        //登录相关
        'core/user_login_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'core/user_login_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'core/create_user_captcha' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'core/get_user_captcha' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //天气
        'weather/get_daily' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'weather/get_hourly' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //用户相关
        //获取用户头像
        'account/get_avatar' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //获取用户文字头像
        'account/get_md_avatar' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //用户信息
        'account/get_user_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'account/get_user_info_higo' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'account/search_user_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//批量查询zhouhong
        'account/create_user_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建用户信息zhouhong
        'account/update_user_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新用户信息zhouhong
		'account/get_max_staff_id' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//根据staff_id 前缀获取最大的staff_id

        //通行证
        //邮箱登录
        'passport/login' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'passport/change_password' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'passport/check_password' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //二次验证MFA
        'passport/otpauth_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'passport/otpauth_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'passport/otpauth_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //获取用户工作信息
        'account/get_work_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'account/create_work_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'account/update_work_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'account/get_all_mls_id' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //获取用户基本信息
        'account/create_user_job_title' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//添加职位zhouhong
        'account/user_job_role_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//职位角色
        'account/user_job_title_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//所有职位title
        'account/user_job_level_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//职位级别
        'account/get_lower_level_user' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//职位级别
        'account/get_update_user_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//职位级别

        //私人信息
        'account/get_personal_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//获取zhouhong
        'account/create_personal_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//插入zhouhong
        'account/update_personal_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新zhouhong
        'account/search_personal_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//搜索zhouhong

        //私密信息
        'account/get_privacy_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//获取zhouhong
        'account/create_privacy_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//插入zhouhong
        'account/update_privacy_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新zhouhong

        //密码过期提醒
        'account/user_pass_out_warn' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

		//扩展信息
        'account/get_user_extended_relation' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //用户提醒
        'notice/notice_info_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notice/notice_info_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'notice/notice_info_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'notice/user_pending_notice_get' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'notice/notice_mark_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //请假相关 haibinzhou
        'hr_leave/leave_absence' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_sick' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_funeral' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_paid_sick' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_marital' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_paternity' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_abortion' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_detection' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_maternity' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_annual' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_annual_admin' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        'hr_leave/get_leave_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/get_leave_total' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        'hr_leave/leave_create' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),


        'hr_leave/leave_update_status' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'hr_leave/calculation_leave_days' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/working_calendar_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'hr_leave/leave_update_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'hr_leave/get_working_calendar' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'hr_leave/update_working_calendar' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'hr_leave/create_working_calendar' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //单据队列
        'approval/add_order_send_queue' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'approval/get_order_send_queue' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'approval/create_order_operate_queue' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'approval/get_order_operate_queue' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'approval/update_order_operate_queue' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'approval/update_order_process' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //职位招聘
        'recruit/get_recruit_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'recruit/recruit_save' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'recruit/recruit_update' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //门禁卡 guojiezhu
        'punch/create_punch_syn_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建需要同步的门禁卡数据 （用户 和 部门）
        'punch/get_punch_syn_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//获取门禁卡队列数据
        'punch/update_punch_syn_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新门禁卡队列数据
        'punch/update_punch_staff_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//同步门禁卡和用户的关系
        'punch/get_punch_staff_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//获取门禁卡和用户的关系
        'punch/create_punch_staff_relation' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//生成门禁卡和用户的关系
        'punch/create_punch_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建门禁卡打卡记录
        'punch/create_punch_crawl_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//抓取门禁卡的日志
        'punch/get_punch_crawl_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//获取门禁卡抓取的数据
        'punch/get_max_punch_crawl_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//获取最大的抓取时间
        'punch/update_punch_crawl_log_status' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//批量更改抓取记录的状态
        'punch/update_punch_crawl_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更改抓取记录的状态
        'punch/get_daily_punch_log' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),// 获取整理之后的打卡信息
        'punch/get_staff_working_hours' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//获取需要考勤的人
        'punch/resolve_daily_punch_log' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//统计解析打卡记录
		'punch/get_working_hours' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),//获取考勤规则
		'punch/create_daily_punch_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建打卡记录
		
        'punch/create_working_hours' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建考勤规则
        'punch/create_working_staff_hours' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//创建考勤人员和考勤规则关系
        'punch/update_working_hours' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更新考勤时间
        'punch/update_working_staff_hours' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),//更改考勤人员和考勤规则关系
        
		//名片相关
        'executive_card/create_visiting_card' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_card/update_visiting_card' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_card/get_card_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

        //附件相关
        'approval/create_order_attachment' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'approval/get_order_attachment' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //log
        'log/create_admin_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'log/get_admin_log' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //快递相关
        'executive_express/create_express_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_express/get_express_detail' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_express/create_order_express' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_express/get_express_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_express/update_order_express' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_express/update_express_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //办公用品相关
        'executive_supply/get_office_supply' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_supply/get_order_office_supply' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_supply/update_order_office_supply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_supply/create_order_office_supply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_supply/get_office_detail' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_supply/create_office_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_supply/update_office_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_supply/update_office_supply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_supply/create_office_supply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),

        //出差相关
        'executive_travel/get_travel_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),

		//外包用户相关
		'account/create_user_outsourcing_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//创建用户信息guojiezhu
		'account/update_user_outsourcing_info' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//更新用户信息 guojiezhu
		'account/get_user_outsourcing_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//获取用户信息  //guojiezhu

        'worker/get_notify_info' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
       'itserver/get_it_account' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'itserver/update_it_account' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'itserver/create_it_account' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'message_send/message_list_add' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'message_send/message_list_get' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'message_send/message_user_list_add' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'message_send/message_user_list_get' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'message_send/message_user_list_delete_by_msg_id' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),


        //固定资产
        'executive_assets/get_assets_supply_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_assets/get_assets_company_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_assets/create_assets_company' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/create_assets_supply' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/create_order_assets' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/create_order_assets_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/create_order_assets_parity' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/update_order_assets' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/update_order_assets_detail' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/update_order_assets_parity' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/delete_order_assets_parity' => array('service' => 'atom', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'executive_assets/get_order_assets_parity_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_assets/get_order_assets_detail_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_assets/get_order_assets_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'executive_assets/get_assets_list' => array('service' => 'atom', 'method' => 'GET', 'opt' => array('timeout' => 3)),




    );

}