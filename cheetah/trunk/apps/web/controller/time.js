function time() {
    return this;
}
var controlFns = {
    'index': function () {
        // 预定，查看我的时间，携带用户信息，url
        var host = this.req.headers.host || 'speed.meilishuo.com'; //取当前请求的域名.
        var user_id = parseInt(this.req.__get.user_id,10) || 0;
        var iw = parseInt(this.req.__get.iw,10) || 1;
        var php = {
            'user_share_list': '/routine/user_share_list?type=share_id',
            'user_time_manage': '/routine/user_time_manage'
        };

        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data.host = host;
            data.user_id = user_id;
            data.share_user_id = user_id;
            data.iw = iw;
            data.params = {userId:user_id,iw:iw};
            data.shareLink = '/time/';
            data.noBasecss = true;
            data._CSSLinks = ['plugin/fullcalendar', 'plugin/tokeninput', 'nifty/bootstrap','plugin/bootstrap-datetimepicker', 'nifty/font-awesome', 'speed/nifty.meeting_time','queen/king', 'speed/common','speed/time/index','speed/time/modal'];
            data._JSLinks = ['plugin/jquery-ui.custom.min'];
            this.render('time/index.html', data);
        });
    }
    , 'subscribe': function () {
        var php = {
            'calendar': '/routine/user_time_calendar'
        };
        this.ajaxTo(php['calendar']);
    }
    , 'share_time': function () {
        // 共享时间
        var php = {
            'user_share_list': '/routine/user_share_list?type=user_id'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = ['plugin/tokeninput', 'nifty/font-awesome', 'speed/common','speed/time/time'];
            this.render('time/share_time.html', data);
        });
    }
    , 'my': function () {
        var userId = parseInt(this.req.__get.user_id,10) || '';
        var iw = parseInt(this.req.__get.iw,10) || 1;
        var t = this.xssFilter.inDoubleQuotedAttr(this.req.__get.t  || '');
        var start = this.xssFilter.inDoubleQuotedAttr(this.req.__get.start  || '');
        var end = this.xssFilter.inDoubleQuotedAttr(this.req.__get.end  || '');

        var php = {
            'user_share_list': '/routine/user_share_list?type=share_id'
        };
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data.share_user_id = userId;
            data.params = {t:t,path:'/time/my/',start:start,end:end,userId:userId,iw:iw};
            if(start && end){
                data.shareLink = '/time/my/?t='+t+'&start='+start + '&end='+end;
            }else{
                data.shareLink = '/time/my/?t='+t;
            }
            data._CSSLinks = ['plugin/bootstrap-datetimepicker','plugin/tokeninput','speed/time/my','speed/time/modal'];
            this.render('time/my.html', data);
        })
    }
};
exports.__create = controller.__create(time, controlFns);