fml.define('fms/contract/validate', ['jquery','fms/common/utils'], function(require, exports) {
    // var validate = function() {};

    var $ = require('jquery');
    var utils = require('fms/common/utils');
        var vRules = {
            required: /\S/,
            number: /^\d+(\.\d+)?$/,
            phone: /^((\+86)|(86))?((\d{11})|(\d{3,4}(-|\s)?\d{6,8}))$/,
            mobile: /^1[3-8]\d{9}$/,
            email: /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/i,
            postcode: /^[0-9]{6}$/,
            url: /(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/i,
            idcard: /(^\d{15}$)|(^\d{17}(\d|X)$)/,
            chinese: /^[\u4E00-\u9FFF]+$/,
            money: /^[1-9]{1}[0-9]{1,2}(,[0-9]{3})*(.[0-9]{1,2})?$/
        };
        var vTips = {
            required: "请填写此字段",
            number: "请填写数字",
            file: "请选择文件",
            max: "最多填写8个字符",
            min: "最少填写2个字符",
            phone: '请填写正确的电话号码',
            mobile: '请填写正确的手机号码',
            email: '请填写正确的电子邮箱',
            rachInfo: '请选择',
            money: '请输入正确的数字'
        };
        var inpSpecial = {
                radio: 'input[type=radio]',
                file: 'input[type=file]'
            };
            
        // showTip
        function showTip(ele, tip) {
                $(ele).attr('title', tip);
                $(ele).tooltip('show');

            }
        //clearTip
        function clearTip(ele) {
            $(ele).tooltip('destroy')
        }
        function redBorder(ele) {
            $(ele).css({
                'border-color': '#f56245'
            })
        }
        function passBorder(ele) {
            $(ele).css({
                'border-color': '#8abe2e'
            })
        }
        $('input,textarea').on('keyup', function() {
            var _this = this;
            passBorder(_this);
        });
        // 如果是数字类型，自动转换成100,000.00这样的格式
        function moneyValidate(_this){
            var moneyVal = $.trim($(_this).val()).replace(/,/g,'');
                // 不为空得时候 自动替换格式
                if(moneyVal==''){
                    // 什么也不做
                }else if(vRules.number.test(moneyVal)) {
                    $(_this).val(utils.formatCurrency(moneyVal));
                    clearTip(_this);
                    return true;
                }else{
                    showTip(_this, vTips.money);
                    console.log('通不过')
                    return false;
                }
        }
        $('.money').on('blur', function() {
                var _this = this;
                var typeNum = $(this).attr('number');
                // 是否是number类型
                if (typeNum == '') {
                    moneyValidate(_this);
                }
            });
        
        $('select').on('change', function() {
            passBorder(this);
        });


        $('input[type="file"]').on('change', function() {
            var fileVal = $(this).val();
            if (fileVal.length > 0) {
                passBorder(this)
            }
        });

        function checkAll() {
            var isPass = [];
            $('form .form-group:not(.supply-hide)').find('input#pricon_id,input[type="text"],input[type="file"],input[type="radio"],input[type="checkbox"],input[type="password"],input[type="hidden"],select,textarea').each(function(i, e) {
                var input_name = $(e).attr('name'),
                    typeNum = $(e).attr('number'),
                    data_val = $.trim($(e).val()),
                    typeFile = $(e).attr('type'),
                    istokenInput = $(e).attr('id') == 'pricon_id' ? true : false,
                    tokenInput = $('.token-input-list'),
                    tipRoot = $(e).parent(),
                    valIsNull = data_val == '' ? true : false,
                    data_required = $(e).attr('data-required');
                    // var reg = /^0?1[3|4|5|8|7][0-9]\d{8}$/;
                 // if (reg.test(18646089878)) {
                    
                 // }else{
                 //      // alert("号码有误~");
                 // };
                // 有data-required 这个属性  必填验证
                if (data_required == '') {
                    if (valIsNull || data_val == 'undefined') {
                        redBorder(e);
                        if (istokenInput) {
                            redBorder(tokenInput);
                        }
                        isPass.push(false);
                    } else {
                        passBorder(e);
                        if (istokenInput) {
                            passBorder(tokenInput);
                        }
                    }
                }
            });
            return isPass;
        }

    return {
        checkAll:checkAll,
        money:moneyValidate,
        showTip:showTip,
        clearTip:clearTip,
        redBorder:redBorder,
        passBorder:passBorder
    }
});