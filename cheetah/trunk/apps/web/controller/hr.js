function document() {
    return this;
}
var controlFns = {
    'index' : function(){
        return this.redirectTo('/hr/leave/apply');
    },
    'leave' : {
        'index' : function(){
            return this.redirectTo('/hr/leave/apply');
        }
        ,'apply' : function(){
            var php = {
                'leaveRequest': '/hr_leave/leave_request',
                'leave_process_preview': '/hr_leave/leave_process_preview',
                'approve_list_get': '/hr_leave/approve_list_get'
            };
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function(data){
                data._CSSLinks = ['plugin/bootstrap-datetimepicker','speed/hr/leave/apply','queen/upload'];
                this.render('hr/leave/apply.html' , data);
            })
        }
        ,'notice' : function(){
            var php = {
                'approve_list_get': '/hr_leave/approve_list_get',
                'leave_instruction':'/hr_leave/leave_instruction'
            };
            var date = new Date();
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function(data){
                data._CSSLinks = ['speed/hr/leave/notice'];
                data.date = date;
                this.render('hr/leave/notice.html' , data);
            })
        }
        ,'approval' : function(){
            var php = {
            };

            var date = new Date();
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function(data){
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','plugin/tokeninput','queen/approval','speed/hr/leave/approval'];
                data.date = date;
                this.render('hr/leave/approval.html' , data);
            })
        }
        ,'my' : function(){
            var php = {};
            var date = new Date();
            this.bindDefault(php);
            this.bridgeMuch(php);
            this.listenOver(function(data){
                data._CSSLinks = ['plugin/bootstrap/daterangepicker','plugin/tokeninput','queen/approval','speed/hr/leave/approval'];
                data.date = date;
                this.render('hr/leave/my.html' , data);
            })
        }
    }
};
exports.__create = controller.__create(document, controlFns);