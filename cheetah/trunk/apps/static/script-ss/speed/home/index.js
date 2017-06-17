fml.define('speed/home/index', ['jquery', 'plugin/moment', 'plugin/bootstrap/datepicker'], function (require, exports) {
    "use strict";
    var $ = require('jquery');
    var moment = require('plugin/moment');
    var calendar = $('#demo-dp-range');

    //新闻图片轮播
    //页面加载即可初始化，之后再加载数据.
    calendar.datepicker({todayHighlight: true}).on('changeDate', function (event) {
        updateDayEvent(event.date);
    }).on('changeMonth', function (event) {
        var start = moment(event.date).format('YYYY-MM-01');
        var end = moment(event.date).add(1, 'month').format('YYYY-MM-01');
        ajaxGetEvent(start, end, updateCalendarView, event.date);
    });

    /**
     * 获取给定时间段的个人时间
     * @param start
     * @param end
     * @param callback
     * @param date
     */
    function ajaxGetEvent(start, end, callback, date) {
        $.getJSON('/aj/time/search', {start_time: start, end_time: end}, function (ret) {
            if (ret.code == 200) {
                callback(ret.data, date);
            } else {
                callback([]);
            }
        });
    }

    function updateMonthView(date) {
        date = date || new Date();
        var start = moment(date).format('YYYY-MM-01');
        var end = moment(date).add(1, 'month').format('YYYY-MM-01');
        //初始化整个月的个人时间.
        ajaxGetEvent(start, end, updateCalendarView, date);
    }

    /**
     *
     * @param events
     * @param date
     */
    function updateCalendarView(events, date) {
        if (!events || !events.length) {
            return;
        }
        var dates = [];
        var tips = [];
        $.each(events, function (k, v) {
            var data1 = v.start.split(' ');
            var data2 = data1[0].split('-');
            var str = data2[1] + '/' + data2[2] + '/' + data2[0];
            var str_index = dates.indexOf(str);
            if (str_index == -1) {
                dates.push(str);
                tips.push(v.title);
            } else {
                tips[str_index] += ',' + v.title;
                // 加备注，
            }
        });
        //删除之后重新渲染.
        calendar.datepicker('remove');

        calendar.datepicker({
            todayHighlight: true,
            // todayBtn:'today',
            beforeShowDay: function (date) {
                // 这个 option返回一个对象，对象的三个属性如 return 中显示的那样。
                //日期的格式，默认为m/d/y. 如果写错，Date类型不能正确转换
                // var dates = ['05/19/2015', '05/24/2015', '05/26/2015'];
                // var dates = dates_before;
                // var tips = ['05/19/2015', '揍你', '05/26/2015'];
                for (var i = 0; i < dates.length; i++) {
                    if (new Date(dates[i]).toString() == date.toString()) {
                        return {enabled: true, classes: 'something_to_do', tooltip: tips[i]};
                        // 这三个参数分别代表：你想高亮的日期是否可选
                        // 想给它们加什么类名，高亮的 在 这个插件中的样式 是 today，其他插件中可能是highlight
                        // 该日期的title 显示什么。比如滑过24号 ，就会显示出揍你。
                    }
                }
                return {enabled: true, classes: ''};
            }
        }).datepicker('setDate', date);//设置指定时间为激活时间（用于选择时高亮。）
    }


    // 把我的时间明细加到当日待办事项中
    function updateDayEvent(day) {
        var today = moment(day).format('YYYY-MM-DD');
        var nextDay = moment(day).add(1, 'days').format('YYYY-MM-DD');
        ajaxGetEvent(today, nextDay, function (events) {
            if (!events || !events.length) {
                $('.todo_today .panel_body div').html('<div class="empty-todo-today">今天还木有事情啦～～</div>');
                return;
            }

            $('.todo_today').find('.img_div').addClass('hide');
            var str = '<ul>';
            for (var i = 0; i < events.length; i++) {
                //data[i].start.format
                var start_time = moment(events[i].start).format('HH:mm');
                var end = moment(events[i].end);
                var end_time = end.format('HH:mm');
                var cls = end.diff(moment()) <= 0 ? 'finished' : '';
                str += '<li class="' + cls + '"><span class="right">' + start_time + '-' + end_time + '</span><span class="mytime_title">' + events[i].title + '</span></li>';
            }
            str += '</ul>';
            $('.todo_today .panel_body div').empty().append(str);
        });
    }

    updateMonthView(new Date());


    /**
     *天气插件
     */
    var skyconsOptions = {
        "color": "#ff80a9",
        "resizeClear": true
    }

    /* Main Icon */
    var skycons = new Skycons(skyconsOptions);
    //skycons.add("demo-weather-xs-icon", Skycons.PARTLY_CLOUDY_DAY);
    var demo_weather_skycon = {
        'demo-weather-skycon-today':$('#demo-weather-skycon-today').val(),
        'demo-weather-skycon-0':$('#demo-weather-skycon-0').val(),
        'demo-weather-skycon-1':$('#demo-weather-skycon-1').val(),
        'demo-weather-skycon-2':$('#demo-weather-skycon-2').val()
    }
    skycons.add("demo-weather-icon-today", Skycons[demo_weather_skycon['demo-weather-skycon-today']]);
    skycons.add("demo-weather-icon-0",Skycons[demo_weather_skycon['demo-weather-skycon-0']]);
    skycons.add("demo-weather-icon-1",Skycons[demo_weather_skycon['demo-weather-skycon-1']]);
    skycons.add("demo-weather-icon-2",Skycons[demo_weather_skycon['demo-weather-skycon-2']]);
    skycons.play();
    setTimeout(function(){
        $('.input_select').focus();
    },500);


    if($('.becauseofyou li').length>5){
        setInterval(function(){
            var a = $('.becauseofyou li:first').clone();
            $('.becauseofyou li:first').remove();
            //console.log(a);
            $('.becauseofyou ul').append(a);
        },6000)
    }else if ($('.becauseofyou li').length==0){
        $('.becauseofyou ul').append('<li style="display: inline-block;width: 100%;height: 35px;text-align: center;margin-top: 7px;position: relative;top: 10px;">相信会有越来越多的小伙伴出现在这里</li>');
    }
    $('#carousel-example-generic .carousel-control').remove();

    if($('.birthdayremit .birth-people').length==0){
        $('.birthdayremit li').removeClass('hide').html('今天过生日的小伙伴还没加入美丽说呢').addClass('nobodyin');
    }
});