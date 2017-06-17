<div class="mail-wrapper">
    <div class="title">
        <p>Hi! {{receive_name}}：</p>
        <h3>{{username}}的申请通过审批啦！{{order_explains}}</h3>
    </div>

    <div class="section">
        <ul class="introduce">
            <li>
                <span class="tt">申请类型</span>
                <span class="ct">{{order_type}}</span>
            </li>

            {if !empty($leave_date) }
                <li>
                    <span class="tt">请假日期</span>
                    <span class="ct">{{leave_date}}</span>
                </li>
            {/if}

            {if !empty($leave_days) }
                <li>
                    <span class="tt">请假天数</span>
                    <span class="ct">{{leave_days}}</span>
                </li>
            {/if}

            {if !empty($leave_memo) }
            <li>
                <span class="tt">申请理由</span>
                <span class="ct">{{leave_memo}}</span>
            </li>
            {/if}

            <li>
                <span class="tt">申请编号</span>
                <span class="ct">{{order_id}}</span>
            </li>
        </ul>
    </div>
</div>

<div class="mail-wrapper">
    <div class="btn-group">
        <a href="http://speed.meilishuo.com{{url}}" class="btn btn-view">查 看</a>
    </div>
</div>