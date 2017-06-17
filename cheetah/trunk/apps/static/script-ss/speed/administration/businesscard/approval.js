fml.define("speed/administration/businesscard/approval",['jquery','component/approval','plugin/store'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');

    store.set('leave-approval',window.location.pathname);

    var option={
        onAfterSearchChange:function(val){
            if(val!=3){
                $('.addpage').val('7');
            }else{
                $('.addpage').val('500');
            }
        },
        onBeforeLoadleft:function(){
            if($('.addpage').length==0){
                $('.status-val').after('<input type="hidden" class="addpage" name="page_size" value="500">');
            }
        },
        btns:[
            {
                title:'同意',
                class:'btn btn-agree list-agree btn-xs'
            },
            {
                title:'驳回',
                class:'btn btn-danger list-reject btn-xs'
            }
        ],
        leftauto:true,
        searchbtn:true,
        isStruss2:false,
        timeSearch:true,
        usernameSearch:true,
        approveStatusSearch:true,
        identifierSearch:false,
        clearbtn:true,
        headercheckOthers:'',
        leftwidth:'326px',
        urlLeft: '/aj/administration/order_approve_get?type=2',
        urlRight :'/aj/administration/get_approve_request',
        url1:'/aj/administration/order_process',
        url2:'/aj/administration/order_process',
        urlApproval:'/aj/administration/card_approve_list',
        nav:[{title:'待审批',val:'3'},{title:'已审批',val:'4,5,6',addclass:'btn-remove'}],
        controltablestyle:'style="min-height:416px;height:416px"',
        //rightinfostyle:'min-height:416px;height:416px',
        othersubmit:[{n:'type',v:'2'}],
        rightauto:true,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);

    $('.tab-pane').delegate('.more','click',function(){
       $(this).remove();
       $('.timeline-entry').removeClass('hide');
    });
    /*
     插件注释
     btns:array,第一行操作栏左侧的两个按钮，参数分别为按钮的value和class，
     如果是同意需包含list-agree，驳回需包含list-reject，催审需包含list-pushon，撤销需包含list-undo

     timeSearch:Booleans,操作栏是否有起止时间筛选操作
     departSearch:Booleans,操作栏是否有部门筛选操作
     usernameSearch:Booleans,操作栏是否有用户名筛选操作
     approveStatusSearch:Booleans,操作栏是否有审批状态筛选操作
     identifierSearch:Booleans,操作栏是否有编号搜索框
     headercheckOthers: 字符串，此处可放其他用于作为筛选条件的html代码

     leftwidth:左侧列表栏宽度
     urlLeft:左侧列表栏数据接口
     urlRight:右侧详情展示数据接口
     url1:同意或催审接口
     url2:驳回或撤销接口
     urlApproval:审批记录数据接口
     urlApplicationHistory:历史申请记录接口

     左侧table artTemplate模板id 为left-table
     为配合宽窄变化时字段隐藏与显示，
     窄时不显示的内容class为hidden-arrow
     宽时不显示的内容class为hidden-fat
     点击显示右侧数据时所需要传的参数，全部写在tr的data里
     通过status-color控制列表颜色
     status-color0 已撤销 status-color5驳回 status-color4 通过
     <script type="text/html" id="left-table">
     <table class="table">
     <tr>
     <th></th>
     <th class="hidden-arrow"></th>
     <th>姓名</th>
     <th class="hidden-arrow">部门</th>
     <th>类型</th>
     <th class="hidden-arrow">请假日期</th>
     <th>天数</th>
     <th class="hidden-arrow">原因</th>
     </tr>
     {{each data as info}}
     <tr class="show-info status-color{{info.status}}" data-order_id="{{info.order_id}}">
     <td><input type="checkbox" data-task_id="{{info.task_id}}" data-order_id="{{info.order_id}}" value="{{info.order_id}}"/></td>
     <td class="hidden-arrow"><img class="avatar" src="{{info.user_avatar}}"/></td>
     <td>
     <div>{{info.name_cn}}</div>
     <div class="hidden-fat">{{info.depart_name}}</div>
     </td>
     <td class="hidden-arrow">{{info.depart_name}}</td>
     <td>
     <div>{{info.absence_name}}</div>
     <div class="hidden-fat">{{info.start_date}}</div>
     </td>
     <td class="hidden-arrow">{{info.leave_date}}</td>
     <td>{{info.length}}天</td>
     <td class="hidden-arrow">{{info.memo}}</td>
     </tr>
     {{/each}}
     </table>
     </script>

     右侧table artTemplate模板id 为right-show
     右侧的同意驳回，催审，撤销，除原有class外，需要加single类来进行单条提交，
     需要提交数据写在data里
     <a href="javascript:void(0);" data-order_id="{{order_id}}" data-task_id="{{task_id}}" class="btn btn-danger single list-agree">同意</a>
     <a href="javascript:void(0);" data-order_id="{{order_id}}" data-task_id="{{task_id}}" class="btn btn-danger single list-reject">驳回</a>
     审批记录需要加approve-history类
     历史申请记录需要加application-history类
     <a href="javascript:void(0);" class="approval-history" data-task_id="{{task_id}}">审批记录</a>
     <a href="javascript:void(0);" class="application-history" data-user_id="{{user_id}}">历史请假记录</a>
     记录将加载于有timeline-wraper类的div中

     此插件配合使用artTemplate，与页面耦合度较高，并有大量写死的地方，在后期仍需开发优化，整理


     */

});
