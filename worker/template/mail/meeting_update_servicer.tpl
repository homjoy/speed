<div class="mail-wrapper">
    <div class="title">
        <p>Hi! {{username}}：</p>
        <h3>{{initiator}}刚刚修改了“{{meeting_topic}}”</h3>
    </div>

    <div class="section service">
        <ul>
            <li>
                <span class="tt">服务需求</span>
                <span class="ct">
                    {if in_array('meeting_service',$changed_fields) }
                        <strong class="important">{{meeting_service}}</strong>
                    {else}
                        {{meeting_service}}
                    {/if}
                </span>
            </li>
        </ul>
    </div>

    <div class="section introduce">
        <ul>
            <li>
                <span class="tt">会议类型</span>
                <span class="ct">
                    {if in_array('meeting_type',$changed_fields) }
                        <strong class="important">{{meeting_type}}</strong>
                    {else}
                        {{meeting_type}}
                    {/if}
                </span>
            </li>
            <li>
                <span class="tt">会议时间</span>
                <span class="ct">
                    {if in_array('meeting_time',$changed_fields) }
                        <strong class="important">{{meeting_time}}</strong>
                    {else}
                        {{meeting_time}}
                    {/if}
                </span>
            </li>
        </ul>
    </div>
    <div class="line"></div>
    <div class="section">
        {foreach $zones as $key=>$zone}
            <ul class="place">
                <li>
                    <span class="tt">会议地点</span>
                    <span class="ct">
                        {if in_array('zones_'.$key.'_place',$changed_fields) }
                            <strong class="important">{{zone['place']}}{if !empty($zone['room_position']) }({{zone['room_position']}}){/if}</strong>
                        {else}
                            {{zone['place']}}{if !empty($zone['room_position']) }({{zone['room_position']}}){/if}
                        {/if}
                    </span>
                </li>
                <li>
                    <span class="tt">参会人员</span>
                    <span class="ct">
                          {if in_array('zones_'.$key.'_users',$changed_fields) }
                              <strong class="important">{{zone['invite_users']}}</strong>
                        {else}
                              {{zone['invite_users']}}
                        {/if}
                    </span>
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