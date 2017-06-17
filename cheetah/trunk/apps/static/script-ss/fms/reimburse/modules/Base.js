fml.define("fms/reimburse/modules/Base", ['jquery', 'plugin/artTemplate','plugin/bootstrap/datepicker', 'plugin/moment', 'fms/common/utils'], function (require, exports) {
    var $ = require('jquery'), notify = require('component/notify'),Template = require('plugin/artTemplate'),
        moment = require('plugin/moment'), utils = require('fms/common/utils');


    function bindDatePicker(input,str) {
        if (input) {
            var startDataStr = moment().subtract(2, 'month').format('YYYY-MM-DD');
            if(str == 'today')
                startDataStr = moment().format('YYYY-MM-DD');
            else if (!str)
                startDataStr = str;

            input.datepicker({
                format: "yyyy-mm-dd",
                startDate: startDataStr,
                language: "zh-CN",
                autoclose: true
            });
        }
    }

    function bindDateRange(input) {
        input.datepicker({
            format: "yyyy-mm-dd",
            startDate: moment().subtract(2, 'month').format('YYYY-MM-DD'),
            language: "zh-CN",
            autoclose: true
        }).on('changeDate', function (e) {
            var input = $(e.target);
            var row = input.closest('.detail-control');

            var start = row.find('[name=startDate]').val(),
                end = row.find('[name=endDate]').val();

            if (!start || !end) {
                row.find('.column-days span').text('0天');
                return;
            }

            var startTime = moment(start);
            var endTime = moment(end);

            var diff = endTime.diff(startTime, 'days');
            row.find('.column-days span').text(diff + '天');
        });
    }

    /**
     * 统计块的报销金额.
     */
    function bindBlockWantedSummary() {
        var $block = this.$block;
        this.$block.on('blur', '[name=billAmount]', function (e) {
            var total = 0.00;
            $block.find('[name=billAmount]').each(function () {
                total += utils.getCurrencyValue($(this).val());
            });
            $block.find('.detail-summary').text(utils.formatCurrency(total));
        }).on('click', '.btn-remove', function () {
            var summaryAmount = utils.getCurrencyValue($block.find('.detail-summary').text());
            var totalAmount = utils.getCurrencyValue($(".form-apply").find('[name=applyAmount]').val());
            var cur_row = $(this).closest('.detail-row');
            var subAmount = utils.getCurrencyValue(cur_row.find('[name="billAmount"]').val());

            summaryAmount -= subAmount;
            totalAmount -= subAmount;
            //同步当前模块下的统计金额
            $block.find('.detail-summary').text(utils.formatCurrency(summaryAmount));
            //同步总的报销金额
            $(".form-apply").find('[name=applyAmount]').val(utils.formatCurrency(totalAmount));
        });
    }

    /**
     *
     * @param modules
     * @param getPrefix
     */
    function bindForm(modules, getPrefix) {
        var $form = $(".form-apply");
        var detail;
        try {
            detail = $.parseJSON($form.attr('data-json'));
        } catch (e) {
            detail = {};
        }
        detail.constant = window.Constant;
        var blocks = {};
        $.each(modules, function (index, moduleName) {
            var module = require('fms/reimburse/modules/' + moduleName);
            blocks[moduleName] = new module({values: detail});
        });

        //阻止跳转.
        $form.on('submit', function (e) {
            e.preventDefault();
            return false;
        });

        /**
         * 自动汇总报销金额.
         */
        $form.on('blur', '[name=billAmount]', function (e) {
            var value = $(this).val();
            if (value) {
                $(this).val(utils.formatCurrency(value));
            }

            var total = 0.00;
            $form.find('[name=billAmount]').each(function () {
                total += utils.getCurrencyValue($(this).val());
            });

            $form.find('[name=applyAmount]').val(utils.formatCurrency(total));

        }).on('focus', '[name=billAmount]', function (e) {
            var input = $(this);
            if (input.val() && input.val() != 0) {
                $(this).val(utils.getCurrencyValue(input.val()));
            } else {
                input.val('');//清空内容.
            }
        });

        function submitForm(check, url, btn, callback) {
            var success = true;
            var data = [];

            //提交之前删除每个模块内的空item
            $form.children().find('.row').each(function(){
                $row = $(this);
                if($row.prop('hidden')){
                    $row.find('.detail-control').remove();
                }
            });

            var count = 0;
            //前两个模块必须校验
            var must_check_success = true;

            $.each(blocks, function (name, module) {
                //数据校验.
                check && module.validate && (success = module.validate());
                if(count ==0 ||count ==1)
                {
                    must_check_success = !success?success:must_check_success;
                }
                count++;
                var prefix = getPrefix(name);
                data = $.merge(data, module.serializeArray(prefix));
            });
            success = must_check_success? success:must_check_success;

            var text = check ? '提交': '保存草稿';

            if (check && !success) {
                btn.removeClass('disabled').text(text);
                return notify.error('请检查申请单内容.');
            }

            btn.addClass('disabled').text('')
                .css({'border':'none'})
                .prepend('<i class="fa fa-spinner fa-spin"></i> '+text+'中');

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (resp) {
                    btn.removeClass('disabled').text(text);
                    callback(resp);
                }
            });
        }


        /**
         * 提交申请单.
         */
        $('.btn-submit').on("click", function () {
            var btn = $(this);
            var url = btn.attr("data-url");
            var redirect = btn.attr("data-redirect");

            submitForm(true, url, btn,function (resp) {
                if (resp.rcode != 200) {
                    return notify.error(resp.rmessage || '提交失败.');
                }

                return notify.success(resp.rmessage, function () {
                    window.location.href = redirect || '/my/apply/reimburse';
                });
            });
        });

        /**
         * 保存草稿，不校验，直接提交数据保存.
         */
        $(".btn-save-draft").on('click', function () {
            var btn = $(this);
            var url = btn.attr("data-url");
            var redirect = btn.attr("data-redirect");

            submitForm(false, url, btn,function (resp) {
                if (resp.rcode != 200) {
                    return notify.error(resp.rmessage || '保存失败.');
                }
                return notify.success(resp.rmessage, function () {
                    window.location.href = redirect || '/my/apply/reimburse';
                });

            });
        });
    }

    /**
     * 检测提交表单模块的有效性－简单校验，是否为空
     * @param fields 需要校验的控件name
     * @param checkType 校验的类型，是费用明晰模块(mutipleLine)or基本信息模块(singleLine)
     * @returns {*}
     */
    function bindCheckValidateResult(fields, checkType) {
        var $block = this.$block;

        function checkEveryItem(that) {
            var success = true;
            $.each(fields, function (index, name) {
                var input = that.find('[name=' + name + ']'), column = (checkType == 'mutipleLine') ? input.parent() : input.closest('.form-group');
                var value = input.val();

                if (!value || value == '0.00') {
                    column.removeClass('has-success').addClass('has-error');
                    success = false;
                } else {
                    column.removeClass('has-error').addClass('has-success');
                }
            });
            return success;
        }

        if (checkType != 'mutipleLine') {
            return checkEveryItem($block);
        }

        var success = true;
        $block.find('.detail-control').each(function () {
            //只有失败的时候标记。否则最后一个的true 会覆盖之前的false
            if(!checkEveryItem($(this))){
                success = false;
            }
        });

        return success;
    }

    /**
     * 将提交的表单数据序列化
     * @returns {Array}
     */

    function bindSerializeArray(prefix,fields,delfield) {
        var $block = this.$block;
        var values = [];

        $block.find('.detail-control').each(function (index) {
            var row = $(this);
            //数组前缀
            var arrPrefix = prefix + '[' + index + '].';

            $.each(fields, function (i, name) {
                var input = row.find('[name=' + name + ']'), value = input.val();
                values.push({name: arrPrefix + name, value: value});
            });
        });

        //增加删除内容.
        values.push({
            name: delfield,
            value: $block.find('[name=' + delfield + ']').val()
        });

        return values;

    }

    /**
     * 费用明晰模块添加行及删除行触发事件
     * @param templateName 新增加的行套用模版名字
     * @param bindDateType 新添加的行需要绑定的组件类型
     * @param delInputName 删除行添加到input控件中，该控件名字
     * @param ctripDayChange 对于交通模块，删除行时需要计算出行天数
     */

    function bindItemAction(templateName,bindDateType,delInputName,ctripDayChange){
        var that = this;
        var $block = this.$block;

        //注册事件，单击"+"展示模块费用明细
        $block.on('click','span.addItemContent',function(){
            $(this).attr('hidden',true)
                .parent().siblings('.row').attr('hidden',false);
        });

        $block.on("click", '.btn-group span', function (e) {
            var row = Template(templateName, {constant: that.detail.constant});
            $block.find('.detail-foot').before(row);

            //初始化日期控件.
            var $row = $block.find('.detail-foot').prev();
            if(bindDateType == 'bindDatePicker')
                bindDatePicker($row.find('[name=billDate]'));
            else
                bindDateRange($row.find(".input-daterange"));

            $row.find('.selectpicker').selectpicker();

        }).on("click", '.btn-remove', function () {
            var btn = $(this);

            var row = btn.closest('.detail-row');
            var id = row.find('[name=id]').val();
            var nowValue = $block.find("[name="+delInputName+"]").val();
            nowValue = nowValue ? nowValue.split(',') : [];
            if (id) {
                nowValue.push(id);
                $block.find("[name="+delInputName+"]").val(nowValue.join(','));
            }


            if($block.find('.detail-control').length == 1) {
                //删除最后一行，并添加新的一行，并隐藏整个表格
                $block.children('.row').attr('hidden',true)
                    .end().find('legend span').attr('hidden',false);

                row.remove();
                //交通模块，同步出行天数
                if(ctripDayChange == "ctripDayChange") bindCtripDateChange(that);
                $block.find('.btn-group span').trigger('click');

                return ;
            }

            row.remove();

            //交通模块，同步出行天数
            if(ctripDayChange == "ctripDayChange") bindCtripDateChange(that);
        });

    }

    function bindUserCTrip(userId,ctripNo){
        var $form = $(".form-apply");

        if(!$form.find('.business-trip').length){
            return ;
        }

        $.post('/aj/reimburse/get_user_ctrip',{userId:userId},function(resp){
            if(resp.rcode != 200){
                return notify.error('加载出差信息出错!');
            }
            var select = $form.find('.business-trip [name=ctripRequestNo]');
            var selectedValue = select.attr('data-selected');
            var options = [
                '<option>请选择</option>'
            ];
            $.each(resp.data,function(index,ctrip){
                options.push(
                    '<option value="'+ctrip.ctripNo+'" '+(ctripNo == ctrip.ctripNo ? 'selected':'')+'>'+ctrip.ctripNo+'</option>'
                );
            });
            select.html(options.join(''));
        },'json');
    }

    function bindComputeCtripDay(){
        var that = this;

        that.$block.on('change','[name=billDate]',function() {
            bindCtripDateChange(that);
        });
    }

    function bindCtripDateChange(that){
         var dateArray = [], ctripDay = 0;

         that.$block.find('[name=billDate]').each(function (index) {
            dateArray.push($(this).val());
         });

         if (dateArray.length == 1) ctripDay = 1;
         else if(dateArray.length > 1) {
             dateArray.sort(function (prev, cur) {
                return moment(cur).diff(moment(prev));
             });
             ctripDay = moment(dateArray[0]).diff(moment(dateArray[dateArray.length - 1]), 'day') + 1;
         }

         if(ctripDay) that.$block.prev().find('[name=ctripDays]').val(ctripDay);
    }


    return {
        bindUpload: utils.bindUpload,
        bindDatePicker: bindDatePicker,
        bindDateRange:bindDateRange,
        bindBlockWantedSummary: bindBlockWantedSummary,
        bindForm: bindForm,
        bindItemAction:bindItemAction,
        bindCheckValidateResult: bindCheckValidateResult,
        bindSerializeArray: bindSerializeArray,
        bindUserCTrip:bindUserCTrip,
        bindComputeCtripDay:bindComputeCtripDay
    }
});