fml.define("speed/meeting/index", ['jquery', 'plugin/store', 'plugin/moment', 'plugin/bootstrap/datetimepicker'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var moment = require('plugin/moment');
    var store = require('plugin/store');

    (function wizard() {
        //无需进行引导
        if (store.get('passOverviewWizard')) {
            return;
        }

        var wizardHTML = '<div class="wizard-container"><div class="wizard wizard-switch-overview"></div></div>';
        var wizard = $(wizardHTML);
        function relocateTips() {
            wizard.css({
                height: $('#container').height()
            });
        }
        wizard.appendTo("body").on('click', function (e) {
            e.preventDefault();
            wizard.fadeOut(function () {
                $(this).remove();
            });
            $('body').css('overflow','auto');
            //点击只看我的会议.
            $(".switch-view .only-self").trigger('click');
            store.set('passOverviewWizard', 1);
            return false;
        });

        relocateTips();
        $(window).on('resize', relocateTips);
        $('body').css('overflow','hidden');
    })();

    /**
     * 初始化分公司数据.
     */
    (function initCity() {
        var cityId = store.get('office_id') || (function(){
                var firstOffice = $('.choose-city .dropdown-menu .city').first();
                if(firstOffice.length > 0){
                    return firstOffice.attr('data-cityId');
                }else{
                    return 2;
                }
            })();

        var btn = $('.choose-city .city[data-cityId=' + cityId + ']');
        var companyName = btn.text();
        $('.choose-city .current-city-name').html(companyName + '<span class="caret"></span>');
        $('.choose-city .city').removeClass('selected');
        //选中当前城市
        btn.addClass('selected');
    })();

    /**
     * 生成会议室列表到页面
     * @param rooms
     */
    function renderMeetingList(rooms) {
        var rows = [];

        function getCls(service) {
            return service['support'] == 'yes' ? ' support ' :
                service['support'] == 'error' ? ' error ' : ' not-support ';
        }

        //服务图标
        var icons = ['m-icon-video', 'm-icon-projector', 'm-icon-phone'];

        var i, len, service, tr, index;
        $.each(rooms, function (idx, room) {
            tr = '<tr id="room_' + room.room_id + '">' +
                    //'<td>' + room.room_position + '</td>' +
                '<td width="50px">' + room['extension'] + '层</td>' +
                '<td width="110px">' + room.room_name + '</td>' +
                '<td width="110px">';

            for (i = 0, len = room.services.length; i < len; i++) {
                service = room.services[i];
                index = Math.floor(service['id'] / 2);
                tr += '<span class="' + icons[index] + getCls(service) + '" title="' + service['name'] + '" alt="' + service['name'] + '"></span>' +
                    '<span class="sr-only">' + service['name'] + '</span>';
            }

            tr += '</td>' +
                    //'<td class="hidden-sm hidden-xs">' + room['extension'] + '</td>' +
                '<td width="60px" class="hidden-md hidden-sm hidden-xs">' + room['room_capacity'] + '</td>' +
                '<td width="64%" class="overview"><a href="/meeting/room/?room_id=' + room['room_id'] +
                '"><div class="overview-bar" id="room_' + room['room_id'] + '_progress"></div></a></td>' +
                '</tr>';

            rows.push(tr);
        });

        $('#meeting-list').html(rows.join(''));

        //会议室刷新之后重新刷新预定情况的预览
        roomBooksOverview();
    }

    /**
     * 会议室预览，浏览指定日期的会议室预定情况
     * @param day
     */
    function roomBooksOverview(day) {
        /**
         * 加载完会议列表才能加载预定预览
         */
        var currentDate = day ? day : $('#to-some-day').val();
        var view = currentView();

        //提取当前条件下的会议室列表
        var rooms = $('#meeting-list tr').map(function () {
            return $(this).attr('id').replace('room_', '');
        }).get();

        //查询指定日期的预定情况进行预览
        $.getJSON('/aj/meeting/overview', {
            day: currentDate,
            room_id: rooms.join(','),
            'view': view
        }, function (response) {
            //检测数据格式
            //if (!$.isPlainObject(response.data) /*|| $.isEmptyObject(response.data)*/) {
            //    console.log('meeting/overview数据不正常。');
            //    return false;
            //}
            var progress = response.data;
            //预定数据
            var row, cls, css = ['bar-success', 'bar-info', 'bar-warning', 'bar-danger'];
            //我的会议只用蓝色.
            if (view == 'self') {
                css = ['bar-info'];
            }
            $.each(progress, function (i, bars) {
                row = [];
                $.each(bars, function (j, book) {
                    cls = ( book['room_id'] == 0 ? 'bar-blank' : css[j % css.length]);
                    //cls = 'bar-'+book.class;
                    //<a class="btn-link add-tooltip" data-toggle="tooltip" href="#" data-original-title="Default tooltip">consectetuer</a>
                    //
                    var content = book['title'];
                    row.push('<div class="add-popover bar ' + cls
                        + '"style="width: ' + book['length']
                        + '%;" data-toggle="popover" data-placement="top" data-original-title="" data-c="' + content + '">'
                        + book['meeting_topic'] + '</div>'
                    );
                });

                $('#room_' + i + '_progress').html(row.join(''));

                $('[data-toggle="popover"]').popover({
                    html: true,
                    trigger: 'hover',
                    template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
                    content: function () {
                        var content = $(this).attr('data-c');
                        var $content = $("<div>").html(content);
                        var title = $content.find('div > span:last').text();
                        if (title.length >= 10) {
                            $content.find('span').width('200px');
                        }
                        return $content.html();
                    }
                }).on("shown.bs.popover", function (e) {
                    //var $content = $(e.target.nextSibling);
                    //var title = $content.find('div > span:last').text();
                    //if(title.length > 20){
                    //    $content.find('span').width('200px');
                    //}
                });
            });
        });
    }

    //function renderRoomBooks(progress)
    //{
    //    ////时间轴
    //    //var timeList = ['<tr>'];
    //    //$.each(times, function(i,item){
    //    //    var timeClass = i%2 == 0 ? 'hidden-sm hidden-xs' : '';
    //    //    timeList.push('<th class="'+timeClass+'"><span class="label label-primary">'+item+'</span></th>');
    //    //});
    //    //timeList.push('</tr>');
    //    //$('#time_list').html(timeList.join(''));
    //}

    /**
     * 查询会议室列表，根据选择的服务和所选城市
     */
    function searchRooms() {
        var serviceId = $('.service-btn.selected').map(function () {
            return $(this).attr('data-serviceId');
        }).get();
        var cityId = $('.choose-city .city.selected').attr('data-cityId') || 1;//提供默认的
        var params = {};
        if (cityId) {
            params['office_id'] = cityId;
        }
        if (serviceId && serviceId.length > 0) {
            params['service_id'] = $.isArray(serviceId) ? serviceId.join(',') : serviceId;
        }
        /*
         查询会议室列表
         */
        $.getJSON('/aj/meeting/rooms', params, function (response) {
            if (response.code !== 200) {
                return false;
            }
            ////格式不正确
            //if (!$.isArray(response.data) || response.data.length == 0) {
            //    console.log('-----获取会议室列表出错', response.data);
            //    return false;
            //}
            renderMeetingList(response.data);
        });
    }

    /**
     * 选中了服务
     */
    $(".service-btn").on("click", function (e) {
        e.preventDefault();
        $(this).toggleClass('selected');
        $(this).find('span').toggleClass('filter');
        searchRooms();
    });

    /**
     * 点击上一天、下一天
     */
    $('.jumpday').on('click', function (e) {
        e.preventDefault();
        var self = $(this);
        $('.jumpday').removeClass('current');
        self.addClass('current');
        var dateFormat = 'YYYY-MM-DD';
        var current = $("#to-some-day").val() || moment().format(dateFormat);
        var day = '';
        if (self.hasClass('to-prev-day')) {
            day = moment(current).subtract(1, 'days').format(dateFormat);
        } else if (self.hasClass('to-next-day')) {
            day = moment(current).add(1, 'days').format(dateFormat);
        } else {
            day = moment().format(dateFormat);
        }
        if (!day) {
            return;
        }

        $("#to-some-day").val(day);
        if (current != day) {
            //reloadRooms();
            roomBooksOverview(day);
        }
    });

    $("#to-some-day").datetimepicker({
        minView: 'month',
        autoclose: true,
        format: "yyyy-mm-dd"
    }).on("changeDate", function (e) {
        // $(this).datetimepicker('hide');
        // roomBooksOverview(e.format());
        roomBooksOverview($(this).val());
    });

    /**
     * 选择城市
     */
    $('.choose-city .city').on('click', function (e) {
        e.preventDefault();
        var self = $(this);

        //同一个不刷新.
        if (self.attr('data-cityId') == $(".choose-city .city.selected").attr('data-cityId')) {
            return;
        }
        //切换到当前城市
        $('.choose-city .current-city-name').html(self.text() + '<span class="caret"></span>');
        //切换选中状态
        $('.choose-city .city').removeClass('selected');
        self.addClass('selected');
        //设置COOKIE
        store.set('office_id', self.attr("data-cityId"));

        //取消选中的服务
        $('.service-btn').removeClass('selected');

        //重新筛选会议室
        searchRooms();
    });


    //初始化加载数据
    searchRooms();

    //展开当前菜单.
    //$('#mainnav-menu-wrap a[href="/meeting"]').parent().addClass('active-link').parent().parent().addClass('active-sub').addClass('active');


    var head = $('#table_head'),_timer;
    var top = head.offset().top - head.height();
    head.affix({
        offset: {top: top}
    }).on("affix.bs.affix",function(){
        syncWidth();
    });
    $(".mainnav-toggle").on("click",syncWidth);

    //表头宽度同步。
    function syncWidth(){

        var container = $("#container");
        if(_timer){
            window.clearInterval(_timer);
        }
        _timer = window.setInterval(function(){
            var head = $('#table_head');
            //先让head 宽度用auto 计算
            head.width('auto');
            //重设置为具体的px
            head.width(head.width());
            //让列表也同步，防止头宽度正常，但是列表还是溢出
            head.next().width(head.width());
            //还原表格宽度
            head.next().width('100%');

            var pWidth = head.parent().width();
            //如果内容还是溢出
            if(head.width() < pWidth && $("#container").width() <= $(window).width()){
                head.width(pWidth);
            }
        },1000);
    }
    $(window).on('resize',syncWidth);
    //置顶
    //$('#table_head').stickUp({});


    /**
     * 获取当前视图
     * @returns {*|jQuery|string}
     */
    function currentView() {
        return $('.switch-view.switched .selected').attr('data-value') || 'all';
    }

    $(".switch-view a").on('click', function (e) {
        e.preventDefault();
        var self = $(this);
        var viewRow = self.parent();

        if (self.hasClass('selected')) {
            self.removeClass('selected');
            viewRow.removeClass('switched');
        } else {
            viewRow.find('.selected').removeClass('selected');
            self.addClass('selected');
            viewRow.addClass('switched');
        }

        roomBooksOverview();
    });
});