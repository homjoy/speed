
<div class="mail-wrapper">
    <div class="title">
        <p>Hi！亲：</p>
        <h3>会议提醒：{{meeting_type}}“{{meeting_topic}}”马上开始啦，请提前准备，辛苦啦！</h3>
    </div>

    <div class="section service">
        <ul>
            <li>
                <span class="tt important">服务需求</span>
                <span class="ct">{{meeting_service}}</span>
            </li>
        </ul>
    </div>

    <div class="section introduce">
        <ul>
            <li>
                <span class="tt">会议类型</span>
                <span class="ct">{{meeting_type}}</span>
            </li>
            <li>
                <span class="tt">会议时间</span>
                <span class="ct">{{meeting_time}}</span>
            </li>
        </ul>
    </div>
    <div class="line"></div>
    <div class="section">
        {foreach $zones as $key=>$zone}
        <ul class="place">
            <li>
                <span class="tt">会议地点</span>
                <span class="ct">{{zone['place']}}{if !empty($zone['room_position']) }({{zone['room_position']}}){/if}</span>
            </li>
            <li>
                <span class="tt">参会人员</span>
                <span class="ct">{{zone['invite_users']}}</span>
            </li>
        </ul>
        {/foreach}
    </div>
</div>

<div class="mail-wrapper">
    <div class="btn-group">
        <a href="http://speed.meilishuo.com/time" class="btn btn-view">查 看</a>
    </div>
    {*<div class="footer">*}
    {*<p class="go-speed">去<a href="http://speed.meilishuo.com/time" class="link-my-time">我的时间</a>看看</p>*}
    {*</div>*}
</div>