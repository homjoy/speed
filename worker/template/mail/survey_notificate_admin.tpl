<div class="mail-wrapper">
    <div class="title">
        <p>Hi! {{admin_name}}:</p>
        <h3>{{user_name}}填写了调查问卷，赶快登陆查看吧！</h3>
    </div>

    <div class="section introduce">
        <ul>
            <li>
                <span class="tt">用户姓名</span>
                <span class="ct">{{user_name}}</span>
            </li>
            <li>
                <span class="tt">用户邮箱</span>
                {$mail_link = 'mailto:'.$user_mail}
                <span class="ct"><a href={{mail_link}}>{{user_mail}}</a></span>
            </li>
        </ul>
    </div>
    <div class="line"></div>
    <div class="section">
        <ul>
            <li>
                <span class="tt">订单编号</span>
                <span class="ct">{{order_id}}</span>
            </li>
            <li>
                <span class="tt">反馈内容</span>
                <span class="ct">{{suggestion}}</span>
            </li>
        </ul>
    </div>

    <div class="btn-group">
        <a href="http://speed.meilishuo.com/survey/index.php?r=admin/survey/sa/index" class="btn btn-view">查 看</a>
    </div>
</div>
