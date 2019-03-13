<?php

$GET = 'GET';
$POST = 'POST,OPTIONS';

return [
    "$POST api/v1/user/login" => 'api/v1/user/login',
    "$POST api/v1/user/logout" => 'api/v1/user/logout',
    "$POST api/v1/user/hold" => 'api/v1/user/hold',
    "$POST api/v1/user/login-refresh" => 'api/v1/user/login-refresh',
    "$POST api/v1/user/registration" => 'api/v1/user/registration',
    "$POST api/v1/user" => 'api/v1/user/detail',
    "$POST api/v1/user/update" => 'api/v1/user/update',
    "$POST api/v1/user/list" => 'api/v1/user/list',
    "$GET api/v1/params/all" => 'api/v1/params/all',
    "$GET api/v1/params/elements" => 'api/v1/params/elements',
    "$POST api/v1/client/list" => 'api/v1/client/list',
    "$POST api/v1/client/get" => 'api/v1/client/get',
    "$POST api/v1/client/add" => 'api/v1/client/add',
    "$POST api/v1/client/remove" => 'api/v1/client/remove',
    "$POST api/v1/client/update" => 'api/v1/client/update',
    "$POST api/v1/lead/list" => 'api/v1/lead/list',
    "$POST api/v1/lead/get" => 'api/v1/lead/get',
    "$POST api/v1/lead/add" => 'api/v1/lead/add',
    "$POST api/v1/lead/remove" => 'api/v1/lead/remove',
    "$POST api/v1/lead/update" => 'api/v1/lead/update',
    "$POST api/v1/product/list" => 'api/v1/product/list',
    "$POST api/v1/note/comment" => 'api/v1/note/comment',
    "$POST api/v1/note/list" => 'api/v1/note/list',
    "$POST api/v1/task/list" => 'api/v1/task/list',
    "$POST api/v1/task/get" => 'api/v1/task/get',
    "$POST api/v1/task/add" => 'api/v1/task/add',
    "$POST api/v1/task/delete" => 'api/v1/task/delete',
    "$POST api/v1/task/close" => 'api/v1/task/close',
    "$POST api/v1/call/list" => 'api/v1/call/list',
    "$POST api/v1/call/add" => 'api/v1/call/add',
    "$POST api/v1/call/sancom" => 'api/v1/call/sancom',
    "$POST  api/v1" => 'api/v1/index',
];
