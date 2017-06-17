fml.define("speed/administration/fixedassets/approval",['jquery','component/notify','component/approval','plugin/store','plugin/bootbox','plugin/artTemplate','component/select'],function(require,exports){
    var $ = require('jquery');
    var store = require('plugin/store');
    var approval = require('component/approval');
    var Template = require('plugin/artTemplate');
    var bootbox = require('plugin/bootbox');
    var notify = require('component/notify');

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
        urlLeft: '/aj/administration/order_approve_get?type=5',
        urlRight :'/aj/administration/assets_approve_request',
        url1:'/aj/administration/order_process',
        url2:'/aj/administration/order_process',
        urlApproval:'/aj/administration/approve_assets_info',
        nav:[{title:'待审批',val:'3'},{title:'已审批',val:'3,4,5,6',addclass:'btn-remove'}],
        othersubmit:[{n:'type',v:'5'}],
        controltablestyle:'style="min-height:416px;height:416px"',
        rightauto:true,
        tokeninputurl:'/aj/address/ajax_search_name'
    }
    approval.approval(option);
    $('.tab-pane').delegate('.edit-button','click',function(){
        var editbtn = this;
        var data = $(this).data();
        var message = Template('edit-modal', data);

        var options = {
            className: "time-modal",
            title: '编辑信息',
            message: message,
            backdrop: true,
            onEscape: function () {
                //关闭对话框.
                //this.modal('hide');
            },
            buttons: {
                cancel: {
                    label: '取消',
                    className: 'btn-default btn-cancel',
                    callback: function () {
                        //暂时不管.
                    }
                },
                success: {
                    label: '确定',
                    className: 'btn-primary',
                    callback: function(){
                        var flag =true;
                        $.each($('.modal-select:not([disabled])'),function(k,v){
                            if(!$(v).val()){
                                console.log(111);
                                notify.error('还有没选的哟');
                                flag=false;
                                return false;
                            }
                        });
                        if($("[name='type']").val()=='1'){
                            if(!$('.first-trhide').find('.form-control').val()){console.log('1price')
                                console.log()
                                notify.error('价格没写哦');
                                flag=false;
                                return false;
                            }
                        }else{
                            if(!$('.first-trhide').find('.form-control,.candisabled').val()){

                                notify.error('价格没写哦');
                                flag=false;
                                return false;
                            }
                        }
                        if(flag){
                            var myForm = $('.form-agree').serializeArray();
                            console.log(myForm);
                            $.post('/aj/administration/manager_submit', myForm, function (ret) {
                                if (ret.code == 200) {
                                    notify.success('操作成功');
                                    $('.show-info.flag').click();
                                } else if (ret.code == 400 || ret.code == 500) {

                                }
                                $('.save_password').removeAttr('disabled');
                            }, 'json');
                        }else{
                            return false;
                        }
                    }
                }
            }
        };
        bootbox.dialog(options);
        $("[name='standard2']").on("change", function () {
            var value = $(this).val();
            $('.selectedinput').val(0);$('.selectedinput'+value).val(1)
        });

        var thlevel = false,thleveldata=false,twleve=false;
        if(data.parity==true){
            //二次编辑
            $(editbtn).parents('.fixedassets').find('.prapareresaulttr').each(function(k,v){
                var jdata = $(v).data();
                console.warn(jdata)
                var fyc=$('.table2 tbody tr')[k];
                if(!!jdata.selected){
                    $(fyc).find('input[name="standard2"]').click()
                }
                $(fyc).find('option').each(function(ka,va){
                    if($(va).html()==jdata.company_name){
                        var s =$(va).attr('value');
                        $(fyc).find('select').val(s);
                    }
                })
                $(fyc).find('.form-control').val(jdata.price);
                if(data.type=='1'){
                    $('.trhide').hide().find('.candisabled').attr('disabled',true);
                }
            })

        }else{
            $('.trhide').hide().find('.candisabled').attr('disabled',true);
        }


        var selectchange = function(para){
            if($(para).hasClass('classid')){
                $.getJSON('/aj/administration/manager_edit_request',{'class_id':$(para).val()}, function (ret) {
                    if (ret.code == 200) {
                        thlevel=ret.data.equipments_infos;
                        thleveldata=ret.data;
                        //第二级为空
                        thleveldata.fyctype='equipment_id';
                        var selcthtml = Template('select-td', thleveldata);
                        $('.equipment_id_td').html(selcthtml);
//第三级为空
                        thleveldata.fyctype='';
                        thleveldata.equipments_infos=[];
                        var selcthtml = Template('select-td', thleveldata);
                        $('.brand_id_td').html(selcthtml);
//第四级为空
                        thleveldata.fyctype='';
                        var selcthtml = Template('select-td', thleveldata);
                        $('.model_id_td').html(selcthtml);

                        $('.select-change').select({
                            //第二级绑定事件
                            'placeholder': "请选择"
                        }).on('change',function(){
                            var _that = this;
                            if($(this).hasClass('equipment_id')){
                                var val = $(_that).val();
                                $.each(thlevel,function(k,v){
                                    if(v.id==val){
                                        v.equipments_infos= v.child;
                                        v.fyctype='brand_id';
                                        twleve= v.equipments_infos;

                                        var selcthtml = Template('select-td', v);
                                        $('.brand_id_td').html(selcthtml);
                                        thleveldata.fyctype='';
                                        v.equipments_infos=[];
                                        var selcthtml = Template('select-td', v);
                                        $('.model_id_td').html(selcthtml);
                                        console.log(twleve);

                                        $('.brand_id,.model_id').select({
                                            //第三四级绑定事件
                                            'placeholder': "请选择"
                                        }).on('change',function(){
                                            var _that = this;
                                            if($(this).hasClass('brand_id')){
                                                var val = $(_that).val();
                                                $.each(twleve,function(key,value){
                                                    if(value.id==val){
                                                        value.equipments_infos= value.child;
                                                        value.fyctype='model_id';
                                                        var selcthtml = Template('select-td', value);
                                                        $('.model_id_td').html(selcthtml);
                                                        $('.model_id').select({
                                                            'placeholder': "请选择"
                                                        })
                                                        return;
                                                    }
                                                })
                                            }
                                        })
                                        return;
                                    }
                                })
                            }
                        })
                    } else if (ret.code == 400 || ret.code == 500) {}
                });
            }else{}
        }
        $('.modal-select').select({
            //第一级绑定事件
            'placeholder': "请选择"
        }).on('change',function(){
            selectchange(this);
        });
        if(data.parity==true){
            var fdata = $(editbtn).parents('.fixedassets').find('.finalchoosetr').data();
            console.log(fdata);
            $('.classid').prev().find('a').each(function(k,v){
                if($(v).html()==fdata.class_name){
                    $(v).click();
                    setTimeout(function(){
                        $('.equipment_id').prev().find('a').each(function(a,b){
                            if($(b).html()==fdata.equipment_name){
                                $(b).click();
                                $('.brand_id').prev().find('a').each(function(c,d){
                                    if($(d).html()==fdata.brand_name){
                                        $(d).click();
                                        $('.model_id').prev().find('a').each(function(e,f){
                                            if($(f).html()==fdata.model_name){
                                                $(f).click();
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    },100)
                }
            });
        }else{

        }
        //库存，新购
        $("[name='type']").on("change", function () {
            var value = $(this).val();
            //var serviceCheckbox = $(this).closest('.form-group').find('[name^=service_id]');
            if (value == '1') {
                $('[name="standard2"][value="0"]').click();
                $('.trhide').hide().find('.candisabled').attr('disabled',true);
            } else {
                $('.trhide').show().find('.candisabled').removeAttr('disabled');
            }
        });
    });

    $('.right-info').delegate('.prapareresaultbtn','click',function(){
        var _this = this;
        var prevdiv = $(_this).prev();
        if(!$(prevdiv).find('.prapareresault').hasClass('hide')){
            $(prevdiv).find('hr,.prapareresault,.prapareresaultspan').addClass('hide');
        }else{
            $(prevdiv).find('hr,.prapareresault,.prapareresaultspan').removeClass('hide');
        }
    })
});
