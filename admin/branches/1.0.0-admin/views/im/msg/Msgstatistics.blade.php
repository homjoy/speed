@extends('layouts.master')

@section('content')
<div>
今日消息数:{{ $msgCount }}<br/>
今日公众号消息数:{{ $msgPublicCount }}
</div>
@endsection

