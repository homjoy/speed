fml.define('fms/common/base',['jquery','plugin/artTemplate','plugin/bootbox','component/notify','component/menu','fms/common/utils'],function(require,exports){
    var $ = require('jquery'),Template = require('plugin/artTemplate');
    var utils = require('fms/common/utils');
    var bootbox = require('plugin/bootbox');
    var notify = require('component/notify');

    /**
     * 用户切换
     */
    $('.user-change').on('click', function () {
        bootbox.dialog({
            title: "用户切换",
            message: '<form class="form-horizontal" method="post" id="switch-user-form"><div class="form-group"><label class="col-md-4 control-label" for="name">用户邮箱前缀</label><div class="col-md-4"><input id="usercode" name="usercode" type="text" class="form-control input-md usercode"></div></div></form>',
            buttons: {
                success: {
                    label: "确定",
                    className: "btn-success",
                    callback: changeUser
                },
                cancel: {
                    label: "取消",
                    className: "btn-default"
                }
            }
        });
        return false;
    });
    $("body").on("submit",'#switch-user-form',function(e){
        e.preventDefault();
        changeUser();
    });
    function changeUser () {
        var usercode = $('#usercode').val();
        if(!usercode){
            notify.error("要切换的用户名不能为空!");
            return ;
        }
        $.post('/aj/user/switch', {'usercode': usercode}, function (data) {
            if (data.rcode == 200) {

                if(window.location.href.indexOf('/my/manage/') != 1) {
                    window.location = '/my/manage/';
                }else{
                    window.location.reload();
                }


            } else {
                notify.error(data.rmessage)
            }
        }, 'json');
        return false;
    }

    /**
     * 处理所有金额显示
     */
    $('.show-money').each(function(){
        var self = $(this);
        var text = self.text(),
            val = self.val();
        if (val) {
            self.val(utils.formatCurrency(val));
        } else {
            self.text(utils.formatCurrency(text));
        }
    });

    /**
     *
     */
    $('body').on('dblclick','[dbl-open]',function(e){
        e.preventDefault();
        var target = $(e.currentTarget);
        var link = target.attr('dbl-open');
        if(link){
            window.open(link,"_blank","",false);
        }
        return false;
    }).on('blur', 'input.currency', function (e) {
        var value = $(this).val();
        if (value) {
            $(this).val(utils.formatCurrency(value));
        }
    }).on('focus', 'input.currency', function (e) {
        var input = $(this);
        if (input.val()) {
            input.val(input.val() == 0 ? '' : utils.getCurrencyValue(input.val()));
        }
    });


    /**
     * 自动渲染模板.
     * @type {*|jQuery|HTMLElement}
     */
    var $container = $(".table-template-container");
    if($container.length) {
        var tpl = $container.attr('data-tpl');

        var detail;
        try{
            detail = window.TPL_DETAIL || $.parseJSON($container.attr('data-json')) || {};
        }catch(e){
            detail = {};
        }

        detail.extClass = $container.attr('data-class');
        $container.html(Template(tpl,detail));
    }

    /**
     * 自动渲染ul模版
     */
    var $container = $(".ul-template-container");
    if($container.length){
        var tpl = $container.attr('data-tpl');
        var liList, detail = {} ;
        try{
            liList = $.parseJSON($container.attr('data-json')) || {};
        }catch(e){
            liList = {};
        }

        detail.liList = liList;
        detail.curTabName = $container.attr('data-name');
        $container.html(Template(tpl,detail));
    }

    /**
     * 自动适应表格
     */
    function adjustTableContainer()
    {
        var $container = $('.overflow-container'),
            $parent = $container.closest('.panel-body');
        $container.width($parent.width());
    }

    $(window).on('scroll resize',adjustTableContainer);
    adjustTableContainer();
});