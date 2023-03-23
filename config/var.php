<?php


 //路由变量配置                            //相当于别名
return [
    'ug'=> config('app.url')."/service/user/get/",
    'ual'=>config('app.url')."/service/user/alter",
    'uals'=>config('app.url')."/service/user/alterslogan",
    'uua'=>config('app.url')."/service/user/uploadavatar",
    'uub'=>config('app.url')."/service/user/uploadbanner",

    'ua'=> config('app.url')."/service/user/active",
    'uf'=> config('app.url')."/service/user/forget",
    'ur'=>config('app.url')."/service/user/register",
    'ul'=>config('app.url')."/service/user/login",
    'ulo'=>config('app.url')."/service/user/logout",


    'al'=>config('app.url')."/service/admin/login",
    'alo'=>config('app.url')."/service/admin/logout",
    'aal'=>config('app.url')."/service/admin/alter/",
    
    'aug'=>config('app.url')."/service/admin/user/get/",
    'aul'=>config('app.url')."/service/admin/user/getlist",

    'ang'=>config('app.url')."/service/admin/notice/get/",
    'anl'=>config('app.url')."/service/admin/notice/getlist",
    'ani'=>config('app.url')."/service/admin/notice/insert",
    'and'=>config('app.url')."/service/admin/notice/del/",
    'ana'=>config('app.url')."/service/admin/notice/alter/",
    'anr'=>config('app.url')."/service/admin/notice/recover/",

    'asg'=>config('app.url')."/service/admin/station/get/",
    'asl'=>config('app.url')."/service/admin/station/getlist",
    'asi'=>config('app.url')."/service/admin/station/insert/",
    'asd'=>config('app.url')."/service/admin/station/del/",
    'asa'=>config('app.url')."/service/admin/station/alter/",
    'asr'=>config('app.url')."/service/admin/station/recover/",

    'alg'=>config('app.url')."/service/admin/location/get/",
    'all'=>config('app.url')."/service/admin/location/getlist",
    'ali'=>config('app.url')."/service/admin/location/insert/",
    'ald'=>config('app.url')."/service/admin/location/del/",
    'ala'=>config('app.url')."/service/admin/location/alter/",
    'alr'=>config('app.url')."/service/admin/location/recover/",

    'sga'=>config('app.url')."/service/system/getaddr/",
    'sla'=>config('app.url')."/service/system/getaddrlist",

    'apg'=>config('app.url')."/service/appoint/get/",
    'apl'=>config('app.url')."/service/appoint/getlist",
    'apa'=>config('app.url')."/service/appoint/approve/",
    'apr'=>config('app.url')."/service/appoint/refuse/",

    'arg'=>config('app.url')."/service/report/get/",
    'arl'=>config('app.url')."/service/report/getlist",
    'ara'=>config('app.url')."/service/report/approve/",
    'arr'=>config('app.url')."/service/report/refuse/",


    'ng'=>config('app.url')."/service/notice/get/",
    'nl'=>config('app.url')."/service/notice/getlist",

    'sg'=>config('app.url')."/service/station/get/",
    'sl'=>config('app.url')."/service/station/getlist",

    'lg'=>config('app.url')."/service/location/get/",
    'll'=>config('app.url')."/service/location/getlist",
    
    'pg'=>config('app.url')."/service/appoint/get/",
    'pl'=>config('app.url')."/service/appoint/getlist",
    //'pd'=>config('app.url')."/service/appoint/del/",
    //'pr'=>config('app.url')."/service/appoint/recover",
    'pi'=>config('app.url')."/service/appoint/insert/",
    'pc'=>config('app.url')."/service/appoint/cancel/",
    'pa'=>config('app.url')."/service/appoint/apply/",

    'rg'=>config('app.url')."/service/report/get/",
    'rl'=>config('app.url')."/service/report/getlist",
    'rd'=>config('app.url')."/service/report/del/",
    'rr'=>config('app.url')."/service/report/recover",
    'ri'=>config('app.url')."/service/report/insert/",
    'rc'=>config('app.url')."/service/report/cancel/",
    'ra'=>config('app.url')."/service/report/apply/",

    'avg'=>config('app.url')."/service/activity/get/",
    'avl'=>config('app.url')."/service/activity/getlist",
    
    'tg'=>config('app.url')."/service/tag/get/",
    'tl'=>config('app.url')."/service/tag/getlist",
    'til'=>config('app.url')."/service/tag/like/",
    'tdl'=>config('app.url')."/service/tag/dellike/",
];
