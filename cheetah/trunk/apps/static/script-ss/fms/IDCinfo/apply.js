fml.define('fms/IDCinfo/apply',['jquery','plugin/moment','component/notify','plugin/tokeninput','fms/common/utils'],function(require,exports){
    var $ = require('jquery')
        , notify = require('component/notify')
        , moment = require('plugin/moment')
        ,utils = require('fms/common/utils');

    var $relatedSelect = $('[name=serverRoomName]'),$content = $('.machineBill-info');
    var preData = [];
    var preData = [];

    //预处理
    (function(){
        //申请日期绑定
        var $applyDataInput = $('[name=applyDateStr]');
        if(!$applyDataInput.val())
            $applyDataInput.val(moment().format('YYYY-MM-DD'));

        //判断当前选项是否为点对点／非点对点
        showContentByValue($('[name=roomType]:checked').val());

        if(this.IDCinfoDetail && this.IDCinfoDetail.rcode == 200){
            var detail = this.IDCinfoDetail.data;

            //edit情况下需要绑定select选项
            if(detail['serverRoomArea']) {
                $('[name=serverRoomArea]').val(detail.serverRoomArea);
                //edit情况下如果机房有值，需要关联找到相关机房信息
                getRealatedRoom(detail.serverRoomArea,detail.serverRoomName);
            }

            //绑定合同信息
            var item = {contract_no:detail['contractNo'],'contract_name':detail['contractNoName']};
            preData.push(item);
            $('[name=contractNo]').val(item['contractNo']);

            //付款方式
            if(detail['payWay']) $('[name=payWay]').val(detail.payWay);
            //链路类型
            if(detail['linkType']) $('[name=linkType]').val(detail.linkType);

            //各种数值统一成money格式
            $('[name$=Price],[name=contractAmount],[name=contractMonthAmount]').each(function(){
                var value = $(this).val();
                if(value) $(this).val(utils.formatCurrency(value));
            });
        }

    })();


    function getRealatedRoom(typeRoom,serverRoomName){
        $.post('/aj/IDCinfo/areaRoomInfo',{type:typeRoom},function(resp){

            if(resp.rcode != 200){
                return notify.error(resp.rmessage);
            }

            $relatedSelect.html('');
            $relatedSelect.html('<option value="">请选择（关联机房）</option>');

            if(resp.data && resp.data.length ){

                var rooms = resp.data;

                rooms.forEach(function(room){
                    $relatedSelect.append('<option value="'+room.itemValue+'">'+room.itemName+'</option>');
                });
            }/*else {
                $relatedSelect.html('<option value="">请选择（关联机房）</option>');
            }*/
            $relatedSelect.val(serverRoomName);

        },'json');
    }
    //机房－关联机房
    $('[name=serverRoomArea]').change(function(){
        var areaRoom = $(this).val();

        getRealatedRoom(areaRoom);
    });

    function loadRelatedContractInfo(contractNo){
        if(!contractNo) return false;

        $.post('/aj/IDCinfo/contractInfo',{contractCode:contractNo}, function(resp){

            if(resp.rcode !=200)
                return notify.error(resp.rmessage || '获取合同相关信息失败！');

            var info = resp.contractInfo;

            $('[name=contractStartDate]').val(info.cabContractBegin);
            $('[name=contractEndDate]').val(info.cabContractEnd);
            $('[name=contractCompany]').val(info.cpbContractCompany);
            $('[name=customerCompany]').val(info.cpbCustomerCompany);
            $('[name=contractAmount]').val(info.cpbAmount);
        },'json');
    }

    //合同－相关信息
    $('[name=contractNo]').tokenInput('/aj/IDCinfo/contractNoInfo',{
        tokenLimit: 1,
        prePopulate: preData,
        propertyToSearch:'contract_name',
        onReady:function(){
            if(preData.length)
                $('[name=contractNo]').val(preData.pop().contract_no);
        },
        onAdd: function (item) {
            //加载对应的数据信息
            $('.token-input-token').css({'white-space':'normal'});
            $('[name=contractNo]').val(item['contract_no']);
            loadRelatedContractInfo(item['contract_no']);
        },
        tokenFormatter:function(item){
            return "<li><p>" +  item['contract_name']+ "</p></li>";
        },
        resultsFormatter:function(item){
            return "<li>" + item['contract_name'] +"</li>";
        },
        onDelete: function (item) {
            //直接清空.
            $('[name=contractStartDate]').val('');
            $('[name=contractEndDate]').val('');
            $('[name=contractCompany]').val('');
            $('[name=customerCompany]').val('');
            $('[name=contractAmount]').val();
        }
    });

    function showContentByValue(value){
        if(value == '0'){
            console.log('hide row is 0');
            $content.find('.hideRow:hidden').removeClass('hide')
                .end().find('.pointType,.reverseHideRow').addClass('hide');

            return false;
        }
        console.log('hide row is 1');

        $content.find('.hideRow').addClass('hide')
            .end().find('.pointType,.reverseHideRow').removeClass('hide');

    }
    //根据选项点对点／非点对点触发事件
    $('body').on('change','[name=roomType]',function(){
        $('[name=roomType]').each(function(){
            $(this).prop('checked',false);
        });
        $(this).prop('checked',true);

        showContentByValue($(this).val());

    });

    //money格式转换
    $('[name$=Price],[name=contractAmount],[name=contractMonthAmount]').on('blur',function(){

        var value = $(this).val().replace(/(^\s+)|(\s+$)/g,"");
        if(value) $(this).val(utils.formatCurrency(value));
    }).on('focus',function(){

        var value = $(this).val().replace(/(^\s+)|(\s+$)/g,"");
        if(value) $(this).val(utils.getCurrencyValue(value));
    });


    //数据校验
    /*
    function checkForm(){
        var $form = $('.form-apply'),
            $selects = $form.find('select:not([name=linkType])'),
            success = true;

        var checkValidField = [
            'serverRoomArea','serverRoomName', 'contractNo','contractMonthAmount',
            'payWay', 'cabinetAmount','cabinetUnitPrice','cabinetPrice',
        ];

        $.each(checkValidField, function (index, name) {
            var input = $form.find('[name=' + name + ']'), column = input.closest('.form-group');
            var checkFlag =  input.closest('.row').hasClass('hide') ? false : true;

            if(checkFlag) {
                var value = input.val();

                if (!value || value == '0.00') {
                    column.removeClass('has-success').addClass('has-error');
                    success = false;
                } else {
                    column.removeClass('has-error').addClass('has-success');
                }
            }
        });
        return success;
    }
*/

    //数据序列化
    function getFormData(){
        var formData = [],prefix = 'serverRoom.';
        var $form = $('.form-apply');

        var fields = [
            'id','roomType','pointA','pointB','serverRoomArea','serverRoomName', 'contractNo','payWay','contractSeq',
            'contractStartDate','contractEndDate','contractAmount','contractCompany','customerCompany','contractMonthAmount',
            'cabinetAmount','cabinetUnitPrice','cabinetPrice',
            'bandWidth','bandUnitPrice','bandPrice','ipAmount','cabinetUnit','powerType',
            'linkType','addExpense','remark'
        ];

        //是否需要转换金额.
        function needReformat(name) {
            return $.inArray(name, ['contractAmount', 'contractMonthAmount', 'cabinetAmount', 'cabinetUnitPrice'
                    ,'cabinetPrice','cabinetAmount','cabinetUnitPrice','cabinetPrice','bandUnitPrice','bandPrice']) !== -1;
        }

        //将隐藏的行删除
        var checkType = $('[name=roomType]:checked').val();
        if(checkType == '0') {
            $form.find('.reverseHideRow').remove();
        }
        else $form.find('.hideRow').remove();

        $.each(fields, function (index, name) {
            var $input = $form.find("[name=" + name + "]");
            //将隐藏的内容排除

            if($input.length){
                var obj = {
                    name: prefix + name,
                    value: $input.val() || $input.text() || ''
                };

                if(name == 'roomType')
                    obj.value = $form.find("[name=" + name + "]:checked").val();

                if (needReformat(name)) {
                    if(obj.value && utils.getCurrencyValue(obj.value))
                        obj.value = utils.getCurrencyValue(obj.value);
                    else obj.value = '';
                }
                formData.push(obj);
            }
        });

        return formData;
    }

    //提交表单数据 —— ajax方法
    function postFormData(notifyErrStr, notifySucStr){
        var that = this;

        $.ajax({
            url: that.data('url'),
            type:'POST',
            data:getFormData(),
            dataType:'json',
            success:function(resp){
                if(resp.rcode !=200){
                    return notify.error(resp.rmessage || notifyErrStr);
                }

                notify.success(resp.rmessage || notifySucStr, function () {
                    window.location.href = that.data('redirect');
                });
            },
            error: function () {
                notify.error('出错了~ ');
            }
        });

    }

    //表单提交
    $('.btn-submit').on('click',function(e){
        e.preventDefault();

       /* if (!checkForm()) {
            notify.error('请检查数据是否填写正确.');
            return false;
        }*/

        postFormData.call($(this),'提交失败','提交申请成功');
        return false;
    });

    //表单保存草稿
    $('.btn-save-draft').on('click',function(e){
        e.preventDefault();

        postFormData.call($(this),'保存失败','保存草稿成功');

        return false;
    });
});