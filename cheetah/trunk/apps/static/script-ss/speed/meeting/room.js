fml.define('speed/meeting/room', ['jquery', 'plugin/moment', 'plugin/store', 'plugin/fullcalendar', 'plugin/tokeninput', 'plugin/bootstrap/datetimepicker', 'component/notify', 'speed/time/modal'], function (require, exports) {
    "use strict";
    var moment = require('plugin/moment');
    var $ = require('jquery');
    var store = require('plugin/store');
    var notify = require('component/notify');
    var Modal = require('speed/time/modal');

    var CALENDAR_VIEW_KEY = 'meeting-room-calendar-view';

    var data = $('#calendar').data();
    var workday = store.get('meeting_only_workday');
    var defaultView = store.get(CALENDAR_VIEW_KEY) || 'agendaWeek';
    var calendarOptions = {
        defaultView: defaultView,
        weekends: !workday,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        minTime: 8,
        maxTime: 24,
        firstDay: 1,//每周从周一开始
        selectable: true,
        selectHelper: true,
        select: function (start, end, allDay) {
            var myDate = new Date();
            if (store.get('OA_MEILISHUO_TIME_TYPE') != 'month' && start < myDate) {
                notify.error('亲，本系统不支持穿越回去哦，请选择其他时间');
                return false;
            } else if (store.get('OA_MEILISHUO_TIME_TYPE') == 'month' &&
                (moment(start).format('YYYY-MM-DD') < moment(myDate).format('YYYY-MM-DD'))) {
                notify.error('亲，本系统不支持穿越回去哦，请选择其他时间');
                return false;
            }
            if (start > myDate || start.getDate() == myDate.getDate()) {
                Modal.add('meeting', start, end, SPEED.rooms,function(success){
                    if(success){
                        window.location.reload();
                    }
                });
            }
        },
        viewDisplay: function (view) {
            // 初始化界面展示
            var room_id = SPEED.rooms[0].id;
            var viewStart = $.fullCalendar.formatDate(view.start, "yyyy-MM-dd HH:mm");
            var viewEnd = $.fullCalendar.formatDate(view.end, "yyyy-MM-dd HH:mm");
            $("#calendar").fullCalendar('removeEvents');
            $.getJSON('/aj/meeting/get_book_list', {
                room_id: room_id,
                book_start: viewStart,
                book_end: viewEnd
            }, function (ret) {
                var data = ret.data || [];
                lastBooks = data;
                for (var i = 0; i < data.length; i++) {
                    var obj = new Object();
                    obj.id = data[i].id;
                    obj.title = data[i].title;
                    obj.color = data[i].color;
                    obj.start = data[i].start;
                    obj.end = data[i].end;
                    obj.users = data[i].users;
                    obj.user_id = data[i].user_id;
                    obj.allDay = data[i].allDay;
                    obj.repeat_type = data[i].repeat_type;
                    obj.editable = data[i].editable;
                    obj.joined = data[i].joined;
                    // obj.meeting_type = data[i].meeting_type;
                    // console.log(obj);
                    calendar.fullCalendar('renderEvent', obj, true);
                }
            }); //把从后台取出的数据进行封装以后在页面上以fullCalendar的方式进行显示
        },
        eventClick: function (event, element) {
            if (event.editable) {
                Modal.update('meeting',event.id,function(success){
                    if(success){
                        window.location.reload();
                    }
                },SPEED.rooms);
            } else {
                if (event.joined) {//加入的会议可以拒绝.
                    Modal.decline('meeting', event.id,function(success){
                        if(success){
                            window.location.reload();
                        }
                    });
                }
            }
        },
        eventDragStart: function (event, jsEvent, ui, view) {
            window.drag_start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss');
            window.drag_end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss');
        },
        eventDragStop: function (event, jsEvent, ui, view) {
        },
        eventDrop: function (event, jsEvent, ui, view) {
            updateTime(event);
        },
        eventResizeStart: function (event, jsEvent, ui, view) {
            window.drag_start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss');
            window.drag_end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss');
        },
        eventResize: function (event, jsEvent, ui, view) {
            updateTime(event);
        }
    };

    function updateTime(event) {
        var start = $.fullCalendar.formatDate(event.start, 'yyyy-MM-dd HH:mm:ss');
        var end = $.fullCalendar.formatDate(event.end, 'yyyy-MM-dd HH:mm:ss');
        Modal.changeTime('meeting', event.id, start, end, function (resp) {
            if (resp.code != 200) {
                notify.error(result.error_msg || '操作失败');
                return;
            }
            //else if (resp.code == 400 || resp.code == 300) {
            //    notify.error(result.error_msg || '操作失败');
            //    event.start = window.drag_start;
            //    event.end = window.drag_end;
            //    //失败了，还原回去.
            //    calendar.fullCalendar('updateEvent', event);
            //}
            notify.success('操作成功', function () {
                window.location.reload();
            });
        });
    }

    var lastBooks = [];
    var calendar = $('#calendar').fullCalendar('destroy').fullCalendar(calendarOptions);

    /**
     * 是否只显示工作日.
     * @returns {boolean}
     */
    function onlyWorkday() {
        return $('.switch-view.switched .selected').attr('data-value') == 'workday';
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
        var workday = !onlyWorkday();
        store.set('meeting_only_workday', workday);
        calendarOptions.weekends = workday;
        calendarOptions.defaultView = store.get(CALENDAR_VIEW_KEY) || 'agendaWeek';
        calendar = $('#calendar').fullCalendar('destroy').fullCalendar(calendarOptions);
    });

    (function initSwitch() {
        var onlyWorkday = !!store.get('meeting_only_workday');
        if (onlyWorkday) {
            $('.switch-view').addClass('switched');
            $('.switch-view .only-workday').addClass('selected');
        }
    })();


    $("body").on("click",".fc-button-month,.fc-button-agendaWeek,.fc-button-agendaDay",function(e){
        var btn = $(this);
        $.each(['month','agendaWeek','agendaDay'],function(index,btnName){
            if(btn.hasClass('fc-button-'+btnName)){
                store.set(CALENDAR_VIEW_KEY,btnName);
            }
        });
    });
});