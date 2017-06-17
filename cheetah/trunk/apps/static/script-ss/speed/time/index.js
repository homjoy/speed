fml.define('speed/time/index', ['jquery', 'plugin/moment', 'plugin/store', 'plugin/tokeninput', 'plugin/fullcalendar', 'plugin/bootstrap/datetimepicker', 'plugin/artTemplate', 'component/notify', 'speed/time/modal'], function (require, exports) {
    "use strict";
    var moment = require('plugin/moment');
    var $ = require('jquery');
    var store = require('plugin/store');
    var notify = require('component/notify');
    var Modal = require('speed/time/modal');
    var CALENDAR_VIEW_TYPE = 'time-index-calendar-view';

    //时间初始化
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();
    window.drag_start = '';
    window.drag_end = '';

    //记录用户选定的时间按钮状态(月，周，日)
    var last_time_type = store.get(CALENDAR_VIEW_TYPE) || 'agendaWeek';


    var $btn =$('button[data-bb-handler="success"]');
    console.log($btn);
    $btn.addClass("edit_btn");
    console.log("I am here!");
    //$('body').delegate('.edit_btn','click',function(){
    //    //alert("123");
    //    console.log(11);
    //});

    //日历初始化
    var calendar = $('#calendar').fullCalendar({
        defaultView: last_time_type,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        minTime: 8,
        maxTime: 24,
        // firstDay:1,
        //防止重叠
        slotEventOverlap: false,
        selectable: true,
        selectHelper: true,

        select: function (start, end, allDay) {
            var now = new Date();
            var calendarViewType = store.get(CALENDAR_VIEW_TYPE) || 'agendaWeek';
            if (calendarViewType != 'month' && start < now) {
                notify.error('亲，本系统不支持穿越回去哦，请选择其他时间');
                return false;
            } else if (calendarViewType == 'month' && (moment(start).format('YYYY-MM-DD') < moment(now).format('YYYY-MM-DD'))) {
                notify.error('亲，本系统不支持穿越回去哦，请选择其他时间');
                return false;
            }

            if (start > now || start.getDate() == now.getDate()) {
                Modal.add('both', start, end,[],function(success){
                    if(success){
                        window.location.reload();
                    }
                });
            }
            ////判断预定会议室tab是否出现
            ////只比较日期，防止当天被隐藏.
            //if(start < now || (calendarViewType == 'month' && start.getDate()==now.getDate())){
            //    $('#myTab').children().last().hide();
            //}else{
            //    $('#myTab').children().last().show();
            //}
        },

        viewDisplay: function (view) {
            var viewStart = $.fullCalendar.formatDate(view.start, "yyyy-MM-dd HH:mm");
            var viewEnd = $.fullCalendar.formatDate(view.end, "yyyy-MM-dd HH:mm");

            $("#calendar").fullCalendar('removeEvents');
            // console.log(viewStart+viewEnd);
            // var user_id = $('#share_user_id').val();
            $.getJSON('/aj/time/search', {
                start_time: viewStart,
                end_time: viewEnd,
                user_id: SPEED.share_user_id
            }, function (ret) {
                var data = ret.data || []; //确保格式为数组.
                for (var i = 0; i < data.length; i++) {
                    var obj = new Object();
                    obj.id = data[i].id;
                    obj.type = data[i].type;
                    obj.time_id = data[i].time_id;
                    obj.title = data[i].title;
                    if (data[i].book_id > 0) {
                        obj.display_title = data[i].display_title;
                    }
                    obj.color = data[i].color;
                    obj.start = data[i].start;
                    obj.end = data[i].end;
                    obj.users = data[i].users;
                    obj.memo = data[i].memo;
                    obj.position = data[i].position;
                    obj.room_name = data[i].room_name;
                    obj.room_position = data[i].room_position;
                    obj.allDay = false;
                    obj.is_repeat = data[i].is_repeat;
                    obj.remind_time = data[i].remind_time;
                    obj.remind_type = data[i].remind_type;
                    obj.editable = data[i].editable;
                    obj.provide_service = data[i].provide_service;
                    obj.name_cn = data[i].name_cn;
                    obj.remind_time = data[i].remind_time;
                    obj.remind_type = data[i].remind_type;
                    calendar.fullCalendar('renderEvent', obj, true);
                }
            });
        },

        eventClick: function (event, element) {
            //个人内容修改
            if (event.type == 'time') {
                Modal.getDetail('time',event.time_id,function(edit){
                   Modal.update('time',event.time_id,function(success){
                        if(success){
                            window.location.reload();
                        }
                    });
                    setTimeout(function(){
                        $('body').addClass('modal-open');

                    },1000);
                });
            }
            //会议室修改
            else if (event.type == 'meeting') {
                Modal.getDetail('meeting',event.id,function(edit){
                    if(event.editable){
                        Modal.update('meeting',event.id,function(success){
                            if(success){
                                    window.location.reload();
                            }
                        });
                        setTimeout(function(){
                            $('body').addClass('modal-open');

                        },1000);
                    }else{
                        Modal.decline('meeting',event.id);
                    }
                });
            }

        },

        eventDragStart: function (event, jsEvent, ui, view) {
            window.drag_event = event;
            window.drag_start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'),
                window.drag_end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss')
        },
        eventDragStop: function (event, jsEvent, ui, view) {
        },
        eventDrop: function (event, jsEvent, ui, view) {
            if ($('#iw').val() == '0') {
                return false;
            }
            if (event.type == 'time') {

                $.post("/aj/time/change_time", {
                    time_id: event.time_id,
                    start_time: $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'),
                    end_time: $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'),
                    user_id: user_id
                }, function (ret) {
                    if (ret.code == 200) {
                        notify.success('时间调整成功');
                    } else if (ret.code == 400 || ret.code == 300) {
                        notify.error(ret.error_msg || '操作失败');
                        event.start = window.drag_start;
                        event.end = window.drag_end;
                        if (ret.code == 400 && ret.data.status == 1) {
                            $('.notice_again_text').html(result.error_msg);
                            $('.id_again').attr('name', 'time_id').val(event.id);
                            $('.start_time_again').attr('name', 'start_time').val($.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'));
                            $('.end_time_again').attr('name', 'end_time').val($.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'));
                            $('#btn_submit_again').attr('submit-url', '/aj/time/change_time');
                            $('#notice_again').modal('show');
                        }
                    }
                    calendar.fullCalendar('updateEvent', event);
                }, "json");
            } else if (event.type == 'meeting') {
                var start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss');
                var end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss');
                Modal.changeTime('meeting',event.id,start,end,function(ret){
                    if (ret.code == 200) {
                        notify.success('时间调整成功');
                    } else{
                        notify.error(ret.error_msg || '调整失败');
                        event.start = window.drag_start;
                        event.end = window.drag_end;
                        if (ret.code == 400 && ret.data && ret.data.status == 1) {

                        }
                    }
                    calendar.fullCalendar('updateEvent', event);
                });
            }

        },

        eventResizeStart: function (event, jsEvent, ui, view) {
            //记录
            window.drag_event = event;
            window.drag_start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss');
            window.drag_end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss');
        },
        eventResize: function (event, jsEvent, ui, view) {
            //
            if ($('#iw').val() == '0') {
                return false;
            }


            if (event.type == 'time') {
                $.post("/aj/time/change_time", {
                    time_id: event.time_id,
                    start_time: $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'),
                    end_time: $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'),
                    user_id: user_id
                }, function (ret) {
                    if (ret.code == 200) {
                        notify.success('时间调整成功');
                    } else if (ret.code == 400 || ret.code == 300) {
                        notify.error(ret.error_msg || '');
                        //还原回去.
                        event.start = window.drag_start;
                        event.end = window.drag_end;

                        //需要确认提交.
                        if (ret.code == 400 && ret.data.status == 1) {
                            $('.notice_again_text').html(result.error_msg);
                            $('.id_again').attr('name', 'time_id').val(event.id);
                            $('.start_time_again').attr('name', 'start_time').val($.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'));
                            $('.end_time_again').attr('name', 'end_time').val($.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'));
                            $('#btn_submit_again').attr('submit-url', '/aj/time/change_time');
                            $('#notice_again').modal('show');
                        }
                    }
                    calendar.fullCalendar('updateEvent', event);
                }, "json");
            } else if (event.type == 'meeting') {
                $.post("/aj/meeting/change_time", {
                    book_id: event.id,
                    book_start: $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'),
                    book_end: $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'),
                    user_id: user_id
                }, function (ret) {
                    if (ret.code == 200) {
                        notify.success('时间调整成功');
                    } else if (ret.code == 400 || ret.code == 300) {
                        notify.error(ret.error_msg || '调整失败.');
                        event.start = window.drag_start;
                        event.end = window.drag_end;
                        if (ret.code == 400 && ret.data.status == 1) {
                            $('.notice_again_text').html(result.error_msg);
                            $('#btn_submit_again').attr('submit-url', '/aj/meeting/change_time');
                            $('.id_again').val(event.id).attr('name', 'book_id');
                            $('.start_time_again').attr('name', 'book_start').val($.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss'));
                            $('.end_time_again').attr('name', 'book_end').val($.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss'));
                            $('#notice_again').modal('show');
                        }

                    }
                    calendar.fullCalendar('updateEvent', event);
                }, "json");

            }

        },
        eventMouseover: function (event, jsEvent, view) {
            showDetail(event, jsEvent);

        },
        eventMouseout: function (event, jsEvent, view) {
            $('.tip').remove();
        }
    });


    // 取消选项
    //$('.btn_return').click(function () {
    //    calendar.fullCalendar('updateEvent', event);
    //});


    function showDetail(obj, e) {
        var eInfo = '<div id="tip" class="bs-callout bs-callout-danger tip" style="display:none;"><ul>';

        if (obj.time_id) {
            eInfo += '<li class="message">' + '事件名称：' + obj.title + '<br/> </li>';
        }
        else {
            eInfo += '<li class="message">' + '会议主题：' + obj.title + '<br/> </li>';
            eInfo += '<li class="message">' + '会议室：' + obj.room_name + '（' + obj.room_position + '）<br/> </li>';
        }
        eInfo += '<li class="clock">' + $.fullCalendar.formatDate(obj.start, "yyyy-MM-dd HH:mm") + '-' + $.fullCalendar.formatDate(obj.end, "HH:mm") + '</li>';
        eInfo += '<li>发起人：' + obj.name_cn + '</li>';
        eInfo += '</ul></div>';
        $(eInfo).appendTo($('body'));
        $('#tip').css({
            "opacity": "0.4",
            "display": "none",
            "left": (e.pageX + 20) + "px",
            "top": (e.pageY + 10) + "px"
        }).fadeTo(600, 0.9);
        //鼠标移动效果
        $('.fc-event-inner').mousemove(function (e) {
            $('#tip').css({'top': (e.pageY + 10), 'left': (e.pageX + 20)});
        });
    }


    //<p class="">亲，本系统不支持穿越回过去，请选择其他时间</p>
    var template = require('plugin/artTemplate');
    var html = template('switch-view', {});
    $("#calendar .fc-header-right").prepend(html);




    $("body").on("click",".fc-button-month,.fc-button-agendaWeek,.fc-button-agendaDay",function(e){
        var btn = $(this);
        $.each(['month','agendaWeek','agendaDay'],function(index,btnName){
            if(btn.hasClass('fc-button-'+btnName)){
                store.set(CALENDAR_VIEW_TYPE,btnName);
            }
        });
    });
});