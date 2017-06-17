fml.define("speed/administration/officesupply/apply", ['jquery','plugin/artTemplate','plugin/bootstrap/select','component/notify','plugin/bootbox' ], function (require, exports) {
    var $ = require('jquery');
    var notify = require('component/notify');
    var bootbox = require('plugin/bootbox');
    var Template = require('plugin/artTemplate');
//已选要申请的
    var choosed = [];

    //加载方法
    var supplyrequest =function(){
        $.getJSON('/aj/administration/supply_request', {supply_name:$('.supplyname').val()}, function (ret) {
            if (ret.code == 200) {
                var listTemplate = Template('supply-request',ret);
                console.warn(ret.data);
                $('.inside').html(listTemplate);
                var listTemplate2 = Template('office-menu-detail',ret);
                $('.office-menu-detail').html(listTemplate2);
                $('.officesupply').each(function(k,v){
                    if(choosed.indexOf($(v).data().id)!=-1){
                        $(v).addClass('active');
                    }
                })
            } else if (ret.code == 400 || ret.code == 500) {
                notify.success('操作失败');
            }
        });
    }
    supplyrequest();
    //点击搜索
    $('.search-icon').click(function(){
        supplyrequest();
    });
    //类目菜单
    //$('.menu-office-btn').click(function(){
    //    if($(this).hasClass('open')){
    //        $(this).removeClass('open');
    //        $('.office-menu-detail').addClass('hide');
    //    }else{
    //        $(this).addClass('open');
    //        $('.office-menu-detail').removeClass('hide');
    //    }
    //});
    $('.head-fix').on('mouseleave',function(){
        $('.office-menu-detail').addClass('hide');
        $('.menu-office-btn').removeClass('open');
    })
    $('.menu-office-btn').on('mouseover',function(){
        $('.office-menu-detail').removeClass('hide');
        $('.menu-office-btn').addClass('open');

    })
    $('.office-menu-detail').delegate('.turn','click',function(){
        $('.menu-office-btn').removeClass('open');
        $('.office-menu-detail').addClass('hide');
    })
    //左侧物品点击，右侧添加
    $('.left').delegate('.officesupply','click',function(){
        $('.pink').removeClass('active');
        var data = $(this).data();
        console.warn(data);
        var id =data.id;
        $(this).addClass('active');
        $('.kong').hide();
        if(choosed.indexOf(id)!=-1){
            //如果之前已添加，滚到相应位置
            $('.pink[data-id="'+id+'"]').addClass('active');
            $('.over-scroll').scrollTop( $('.pink[data-id="'+id+'"]')[0].scrollHeight-30 );
        }else{
            //如果没添加，则添加
            if(data.is_floor){$('.floornum').removeClass('hide')}
            choosed.push(id);
            var listTemplate = Template('supply-sth',data);
            $('.right .chooseul').append(listTemplate);
            $('.choosenum').html(choosed.length);
            $('.over-scroll').scrollTop( $('.over-scroll')[0].scrollHeight );
        }
    });

    //删除
    $('.right').delegate('li','mouseover',function(){
        $(this).find('.garbage').removeClass('hide');
    });
    $('.right').delegate('li','mouseleave',function(){
        $(this).find('.garbage').addClass('hide');
    });
    $('.right').delegate('.garbage','click',function(){
        var isfloor = false;
        var id =$(this).data().id
        var a = choosed.indexOf(id);
        $('.officesupply.active[data-id="'+id+'"]').removeClass('active');
        choosed.splice(a,1);
        $(this).parents('li').remove();
        $('.choosenum').html(choosed.length);
        $('.chooseul li').each(function(k,v){
            if($(v).data().isfloor=='y'){
                isfloor=true;
            }
        })
        if( $('.chooseul li')==0){
            $('.kong').show();
        }
        if(!isfloor){
            $('.floornum').addClass('hide')
        }
    });

    //加减
    $('.right').delegate('.minus','click',function(){
        var a = parseInt($(this).next().val())-1;
        if (a !=0){
            $(this).next().val(a);
        }else{
            notify.error('数量最少是一个哦，如需删除，请点右面删除按钮');
        }
    });

    $('.right').delegate('.plus','click',function(){
        var a = parseInt($(this).prev().val())+1;
        $(this).prev().val(a);
    });


    //提交功能
    $('.submit').click(function(){
        var datatotal = [];
        $.each($('.chooseul li'),function(k,v){
            var data = $(v).data();
            data.number=$(v).find('input').val();
            datatotal.push(data);
        });
        var floornum = $('.floornum').val();

        if(datatotal.length>0){
            if($('.floornum').hasClass('hide')||!!floornum){
                $.post('/aj/administration/supply_submit',{data:datatotal,floor:floornum}, function (ret) {
                    console.log(ret);
                    if (ret.code == 200) {
                        console.log(1);
                        notify.success('操作成功');
                        window.location.href='/administration/officesupply/my?autuloadlast';
                    } else if (ret.code == 400 || ret.code == 500) {
                        notify.error('操作失败');
                    }
                },'json');
            }else{
                notify.error('亲，选下楼层哈，O(∩_∩)O~');
            }
        }else{
            notify.error('没有选中的物品呢');
        }
    });
    //滚动事件
    var flag = '';
    $(window).scroll(function(){
        var a =$('.head-fix ').offset().top-50;
        var b =$(window).scrollTop();
        if(b>=a&&flag==''){
            flag=a;
            $('.head-fix').addClass('fix').after('<div class="panel-heading panel-heading-holdplace"></div>');
            var offset = $('.panel.right').offset();
            //console.log(offset,position)
            $('.right').addClass('fixright').css({'left':offset.left+'px'})
        }
        if(flag!=''&&b<flag){
            $('.head-fix').removeClass('fix');
            $('.panel.right').removeClass('fixright');
            flag=''
            $('.right').removeAttr('style');
            $('.panel-heading-holdplace').remove()
        }

    });
    $(document).on('keydown', function (e) {
        var e = e || event;
        var currKey = e.keyCode || e.which || e.charCode;
        if (currKey == 13) {
            $('.search-icon').click();
        }
    });
    $('.supplyname').on('input', function(){
        if($(this).val()==''){
            $('.search-icon').click();
        }
    });
});