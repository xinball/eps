<?php


 //路径变量配置                            //相当于别名
return [
    'vaa'=> config('app.url')."/admin/appoint/",
    'vua'=> config('app.url')."/user/appoint/",
    'vhorizon'=> config('app.url')."/horizon",
    'vbt'=> "https://yono.top:8888/tencentcloud",
    'vredis'=> "https://yono.top/redis",
    
    'vu'=> config('app.url')."/user/",

    'ug'=> config('app.url')."/service/user/get/",
    'ual'=>config('app.url')."/service/user/alter",
    'uals'=>config('app.url')."/service/user/alterslogan",
    'uua'=>config('app.url')."/service/user/uploadavatar",
    'uub'=>config('app.url')."/service/user/uploadbanner",

    'ua'=> config('app.url')."/service/user/active",
    'ui'=> config('app.url')."/service/user/ipverify",
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
    'asu'=>config('app.url')."/service/admin/station/uploadavatar/",
    'asr'=>config('app.url')."/service/admin/station/recover/",
    
    'sga'=>config('app.url')."/service/system/getaddr/",
    'sla'=>config('app.url')."/service/system/getaddrlist",
    'sgo'=>config('app.url')."/service/system/getoperation",
    
    'aag'=>config('app.url')."/service/admin/area/get/",

    'apg'=>config('app.url')."/service/admin/appoint/get/",
    'apl'=>config('app.url')."/service/admin/appoint/getlist",
    'apa'=>config('app.url')."/service/admin/appoint/approve/",
    'apr'=>config('app.url')."/service/admin/appoint/refuse/",

    'arg'=>config('app.url')."/service/admin/report/get/",
    'arl'=>config('app.url')."/service/admin/report/getlist",
    'ara'=>config('app.url')."/service/admin/report/approve/",
    'arr'=>config('app.url')."/service/admin/report/refuse/",


    'ng'=>config('app.url')."/service/notice/get/",
    'nl'=>config('app.url')."/service/notice/getlist",

    'sg'=>config('app.url')."/service/station/get/",
    'sl'=>config('app.url')."/service/station/getlist",

    'lg'=>config('app.url')."/service/location/get/",
    'll'=>config('app.url')."/service/location/getlist",
    
    'pg'=>config('app.url')."/service/appoint/get/",
    'pl'=>config('app.url')."/service/appoint/getlist",
    'pd'=>config('app.url')."/service/appoint/del/",
    'pr'=>config('app.url')."/service/appoint/recover/",
    'pi'=>config('app.url')."/service/appoint/insert/",
    'pc'=>config('app.url')."/service/appoint/cancel/",
    'pa'=>config('app.url')."/service/appoint/apply/",
    'pal'=>config('app.url')."/service/appoint/alter/",

    'rg'=>config('app.url')."/service/report/get/",
    'rl'=>config('app.url')."/service/report/getlist",
    'rd'=>config('app.url')."/service/report/del/",
    'rr'=>config('app.url')."/service/report/recover",
    'ri'=>config('app.url')."/service/report/insert/",
    'rc'=>config('app.url')."/service/report/cancel/",
    'ra'=>config('app.url')."/service/report/apply/",
];
