<?php
namespace Admin\Package\Helper;

class UrlCheckHelper {

	public static function getUrlList() {
		// key：访问的url；value：归属的菜单url
		return array(
			'/workflow/process/process_info'  => '/workflow/process/index',
			'/workflow/task/show_task_detail' => '/workflow/task/manage',
			'/workflow/task/manual_transfer'  => '/workflow/task/manage',
            '/structure/user/update_user'  => '/structure/user/user_home',
            '/structure/user/add_user'  => '/structure/user/user_home',
            '/structure/outer/update_add_user'  => '/structure/outer/user_home',
            '/structure/depart/application_edit'  => '/structure/depart/depart_home',
            '/structure/depart/add_title'  => '/structure/depart/depart_home',
            '/structure/depart/depart_leader_home'  => '/structure/depart/depart_home',
            '/structure/depart/depart_home_backup'  => '/structure/depart/depart_home',
            '/hr/leave/personal_leave'  => '/hr/leave/leave_home',
            '/hr/leave/leave_update'  => '/hr/leave/leave_home',
            '/meeting/meeting_upsert'  => '/meeting/home',
            '/meeting/meeting_book'  => '/meeting/home',
            '/meeting/service_upsert'  => '/meeting/home',
			'/mail/mail_user_list'  => '/mail/mail_home',
            '/hr/attendance/attendance_group'  => '/hr/attendance/attendance_home',
            '/hr/attendance/attendance_user'  => '/hr/attendance/attendance_home',
            '/prompt/update_add_prompt'  => '/prompt/prompt_home',
            '/stationery/apply'  => '/stationery/home',
             '/auth/tree/permissions_add'  => '/auth/role/role',
            '/itserver/account_delete'  => '/itserver/account',
            '/message_send/message_user_list'  => '/message_send/message_list',
            '/message_send/message_upsert'  => '/message_send/message_list',
            '/assets/assets_company_home'  => '/assets/assets_supply_home',
			 '/hr/working_calendar/working_calendar_upsert'=> '/hr/working_calendar/working_calendar_home' ,
			'/mail/mail_group_list' => '/mail/mail_home',
		);
	}
}