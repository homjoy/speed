<?php
namespace Libs\Serviceclient\Api;

class DootaApiList extends \Libs\Serviceclient\Api\ApiList {
    protected static $apiList = array(
		'cargo/cargo_main' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'cargo/cargo_latest' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'cargo/shop_info' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'cargo/cargo_activity' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'cargo/cargo_info' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'cargo/property_info' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'cargo/cargo_shelf' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'cargo/cargo_prop_format' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    	'cargo/cargo_stock' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
    		
        //kuai100回调接口
		'order/kuai100_callback' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/shopping_cart' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'order/shopping_cart_poster' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),

		// 支付相关
		'order/send_to_pay' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/fetch_url' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/wechat_store_fetch' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/wechat_pc_fetch' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/mob_qq_fetch' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'bank/bank_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'bank/bank_platform' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'bank/bank_only' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'mpay/mpay_wechat' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'mpay/mpay_wechat_pc_notify' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/order_pay_check' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		// 用户中心绑定银行卡
		'bank/card_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		// 用户中心解绑银行卡
		'bank/card_unbind' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),

        // 支付相关回调
        'mpay/mpay_notify' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
        'mpay/mpay_danbao_cancel_notify' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
        'mpay/mpay_refund_notify' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),

        //快捷免单活动
        'mpay/mpay_free_charge_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),


        // 地址
		'order/addr_add' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),

        // 活动相关
        'promote/activity_detail' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
        'promote/activity_item_list' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
        'promote/promote_sameshop' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'promote/goods_info' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/activity_top_push' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/activity_group_list' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/activity_ads' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/promote_hotsales' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/promote_act' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'promote/marquee_get' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),

		//订单详情页
        'order/order_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//根据goods_id获取订单量
		'order/goods_order_number' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //订单详情（简版，用于派友回调获取买家id,即seller_uid）
		'order/order_detail' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//订单物流信息
		'order/order_express_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//添加到购物车
		'order/shopping_cart_add' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//添加到购物车的结果数据
		'order/shopping_cart_get' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//twitter_id是否美妆特卖及所属活动信息
		'order/mz_check' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'order/order_num' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'order/order_list_brd' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'order/deliver_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'order/addr_save' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        // 更新购物车
        //[更新数量]
        'order/shopping_cart_update' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //[更新尺寸和颜色]
		'order/shopping_cart_edit' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'order/shopping_cart_number' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		// 删除购物车
		'order/shopping_cart_remove' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//获取表t_bat_shopcart数据
		'order/shopping_cart_model' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//取得partner_info: qq, tel
		'shop/partner_info' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//订单初始化
		'order/order_init' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'order/order_entry' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'shop/shop_num' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//推活动信息
		'cargo/twitter_activity' => array('service' => 'virus', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//合并购物车
        'order/shopping_cart_merge' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
        //快递公司列表
        'express/company_list' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 4)),
        //更新退货物流信息
        'refund/refund_express_update' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),
        //根据rid 或order_id查询退款信息
        'refund/refund_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
        'refund/refund_newlist' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
        'refund/refund_compute' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		//订单列表
		'order/order_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		// 搭配购一键加入购物车
		'order/shopping_cart_add_multi' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),

		//订单列表（通用）
		'order/orders_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//用户订单总数
		'order/order_total_number' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//确认收货
		'order/order_recv_confirm' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		//订单删除
		'order/order_hide' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//退货款详情
		'refund/refund_detail_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		//退货款申请
		'refund/refund_init' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		//退货退款申请提交接口
		'refund/refund_apply' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 6)),
        //填写退货物流信息接口
        'refund/refund_express' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//退款退货取消接口
        'refund/refund_cancel' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        'refund/refund_reapply' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        //退款退货编辑接口
		'refund/refund_edit' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'refund/refund_detail' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//退货退款理由查询接口
		'refund/refund_reason' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		// 退货修改退款原因次数
		'refund/refund_edit_reason_times' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'coupon/get_available_coupons_for_single_sku' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'coupon/get_available_coupons_for_cart' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		'coupon/freeze_coupon' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		//获取直接下单可选的店铺优惠券
		'coupon/get_available_shop_coupons_for_single_sku' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		//获取购物车下单可选的店铺优惠券
		'coupon/get_available_shop_coupons_for_cart' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		'coupon/get_available_platform_coupons_for_single_sku' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		'coupon/get_available_platform_coupons_for_cart' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		'coupon/coupon_list_pc' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		'coupon/coupon_notify' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
		//
		'mpay/mpay_pay_channel' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		//
		'mpay/mpay_pay_switch' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/order_address' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//地址查询
		'order/addr_query' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//地址查询
		'order/addr_select' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//批量购买下订单
		'order/order_add_multi' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		//直接购买下订单
		'order/order_add' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/order_add_weixin' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/addr_validate' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/addr_delete' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'order/addr_default' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),

		// mob快捷支付监测订单时候存在运费变更
		'order/order_prefix' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),

		//批量冻结订单
		'coupon/batch_freeze_coupon' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		//直接下单可用的微信直减
		'coupon/get_weixin_coupon_for_single_sku' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//购物车下单可用的微信直减
		'coupon/get_weixin_coupon_for_cart' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//解冻优惠券
		'coupon/batch_unfreeze_coupon' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'coupon/get_instant_coupon_for_for_cart' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'coupon/get_instant_coupon_for_single_sku' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'coupon/coupon_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		//分类订单
		'order/order_list_classify' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 10)),
		'order/order_feedback' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		//订单的tids
		'order/tids' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 5)),
    	//晒单活动
    	'order/order_shopping_show' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
    	//QR展示
    	'order/qr_show_check' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
    	'order/qr_never_show' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
		'order/order_model' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
		// 关闭订单
		'order/order_close' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),
		// 关闭总单
		'order/order_close_multi' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),
		// 催促卖家发货消息
		'order/order_notify_seller' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),
		// 更新未支付的订单信息
		'order/unpayed_update' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),

        //店铺装修相关得接口
        'shop/category_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'shop/shop_head_img' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'shop/showcase_get' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'shop/shop_top_banner_get' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
	'freight/get_charge' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
         
        //积分接口
        'member/get_free_point' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),      
        'member/consume_point' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),        
		//团购相关写接口
		'groupon/groupon_apply' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'groupon/groupon_edit_audit' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		'groupon/groupon_statitics_add' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 10)),
		//订单优惠数据
		'order/order_favorable_list' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'freight/get_campaign' => array('service' => 'doota', 'method' => 'GET','opt' => array('timeout' => 3)),
		'coupon/get_shop_coupon_apply' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 5)),
        //根据条件获取campaign_goods_info表信息
        'shopevent/get_campaign_goods_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        //根据条件获取campaign_info表信息
        'shopevent/get_campaign_info' => array('service' => 'doota', 'method' => 'GET', 'opt' => array('timeout' => 3)),
        'order/order_update' => array('service' => 'doota', 'method' => 'POST', 'opt' => array('timeout' => 3)),
        
    );
}

