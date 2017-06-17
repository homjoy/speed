function meeting() {
    return this;
}
var controlFns = {
    'index': function () {
        // 所有会议室展示
        var php = {
            // 获得所有room信息，根据room_id筛选
            'get_room': '/meeting/get_room',
            'city': '/company/office_info',
            'services': '/meeting/get_services?type=0',
            'manager': '/book/get_room_manager'
        };
        //获取今天的日期格式.
        var date = new Date();
        var month = date.getMonth() + 1;
        month = month < 10 ? '0' + month : month;
        var day = date.getDate();
        day = day < 10 ? '0' + day : day;
        var today = date.getFullYear() + '-' + month + '-' + day;

        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data.today = today;
            data._CSSLinks = ['plugin/bootstrap-datetimepicker','speed/meeting/index'];
            this.render('meeting/index.html', data);
        });
    }
    , 'room': function () {
        var room_id = parseInt(this.req.__get.room_id || 0,10);
        var php = {
            'get_room': '/meeting/get_room?room_id=' + room_id
        };

        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data.room_id = room_id;
            if(data.get_room.code == 200 && data.get_room.data && data.get_room.data[0] ){
                var serviceIds = [];
                data.get_room.data[0].services.forEach(function(value,index){
                    if(value.support == 'yes'){
                        serviceIds.push(value.id);
                    }
                });
                data.serviceIds = serviceIds.join(',');
            }else{
                data.serviceIds = '';
            }

            var now = new Date();
            data.currentDate = [now.getFullYear(),'年',now.getMonth()+1, '月',now.getDate(),'日'].join('');
            data.noBasecss = true;
            data._CSSLinks = ['plugin/fullcalendar', 'plugin/tokeninput', 'nifty/bootstrap', 'plugin/bootstrap-datetimepicker', 'nifty/font-awesome', 'speed/nifty.meeting_time', 'speed/common','queen/king','speed/time/modal'];
            data._JSLinks = ['plugin/jquery-ui.custom.min'];
            this.render('meeting/room.html', data);
        })
    },
    'map':function(){
        var php = {};
        this.bindDefault(php);
        this.bridgeMuch(php);
        this.listenOver(function (data) {
            data._CSSLinks = [];
            this.render('meeting/map.html', data);
        });
    }
};
exports.__create = controller.__create(meeting, controlFns);