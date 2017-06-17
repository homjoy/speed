$(function(){

   $('#send_sms_code').click(function(){
       var phone = $("#user_phone").val();
        if (user_phone.value.length < 11) {
            show_message(400, '请输入完整的手机号码');
            return false;
        }
       $.post('/exam/Ajax_send_sms_code',{user_phone:phone},function(result){
           show_message(result.code,result.message);
       },'json');
   });

   $('#sms_code').keyup(function(){
       var phone = $("#user_phone").val();
       var len = $(this).val().length;
       var sms_code = $('#sms_code').val();
       if (len == 6) {
           $.post('/exam/Ajax_get_sms_code',{user_phone:phone},function(result){
               if (result.data == sms_code) {
                    $('#code_tips').removeClass('glyphicon-minus-sign text-danger').addClass('glyphicon-ok-sign text-success').show();
                    $('#status').val(1);
               }
               else {
                    $('#code_tips').removeClass('glyphicon-ok-sign text-success').addClass('glyphicon-minus-sign text-danger').show();
                    $('#status').val(0);
               }
           },'json');
       }
   });

   $('#submit').click(function(){
        var exam_type = document.getElementById('exam_type');
        var user_name = document.getElementById('user_name');
        var user_phone = document.getElementById('user_phone');
        if(user_name.value == '' || user_phone.value == '' || exam_type.value == ''){
            show_message(400, '试题类型、手机和姓名不能为空哦');
            return false;
        }
        if (user_phone.value.length < 11) {
            show_message(400, '请输入完整的手机号码');
            return false;
        }
        if ($('#status').val() != 1) {
            show_message(400, '验证码错误');
            return false;
        }
   });

});


