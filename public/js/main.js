/*
 * @Author: XinBall
 * @LastEditors: XinBall
 */
//document.onselectstart = function(){return false;}

// const { forEach } = require("lodash");

        //javascript: window.open( `https://video-direct-link.vercel.app/bili.mp4?aid=${aid}&bvid=${bvid}&cid=${cid}`)
        let editorconfig={
            placeholder:"在此处输入或粘贴内容",
            link:{
                addTargetToExternalLinks: true,
                decorators: [
                    {
                        mode: 'manual',
                        label: '允许下载',
                        attributes:{
                            download: 'download'
                        }
                    }
                ]
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side',
                    'linkImage'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties'
                ]
            },
            mediaEmbed:{
                extraProviders:[
                    {
                        name:'allow-all',
                        url:/^.+mp4/,
                        html:match=>
                            `<div>
                                <iframe src=${match} frameborder="10" allowfullscreen="true" style="position:absolute;width:99%;height:400px;"></iframe>    
                            </div>`
                    }
                ]
            }
        };

    function setproblemsta(sta,stadata,title="") {
    sta.setOption({
        title:{
        left:'center',
            text:title,
        },
        tooltip: {
        trigger: 'item',
        formatter: '{b} : {c}({d}%)'
        },
        legend: {
        bottom: 0,
        left: 'center',
        data: ['AC','CE','WA','RE','TL','ML','SE'],
        },
        series: [{
        type: 'pie',
        radius: ['30%','70%'],
        center: ['50%','40%'],
        selectedMode: 'single',
        itemStyle: {
        borderRadius: 4,
        borderColor: '#fff',
        borderWidth: 1
        },
        label: {
        show: false,
        position: 'center'
        },
        emphasis: {
        itemStyle:{
        shadowBlur:5,
        shadowOffsetX:0,
        shadowColor:'rgb(0,0,0,0.5)'
        },
        label: {
        show: true,
        fontSize: '40',
        fontWeight: 'bold'
        }
        },
        color:[
        "#64c06c",
        "grey",
        "red",
        "sandybrown",
        "orange",
        "deepskyblue",
        "black",
        ],
        data: stadata
        }
        ]
    });
    }
const DAYTYPE=["周日","周一","周二","周三","周四","周五","周六"];
$(function (){
    $(".alert-dismissible").on("click",function () {
        $(this).remove();
    });
    $(document.body).on("click",".alert-dismissible",function () {
        $(this).remove();
    });
    document.body.onselectstart=document.body.ondrag=function(){ 
        return false; 
    }

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
});
//密码小眼睛
function changePwdtype(obj,id){
    if($("#"+id).attr("type")==="password"){
        $("#"+id).attr("type","text");
        $(obj).children()[0].className="bi bi-eye-fill";
    }else{
        $("#"+id).attr("type","password");
        $(obj).children()[0].className="bi bi-eye-slash";
    }
    return false;
}
//返回时间
function getSubTime(start,end){
    let second=Math.floor((end-start)/1000);
    if(second<60)
        return second+"秒";
    else if(second<3600)
        return Math.floor(second/60)+"分钟"+(second%60!=0?Math.floor(second%60)+"秒":"");
    else if(second<86400)
        return Math.floor(second/3600)+"小时"+(second%3600!=0?Math.floor((second%3600)/60)+"分钟":"");
    else if(second<86400*30)
        return Math.floor(second/86400)+"天"+(second%86400!=0?Math.floor((second%86400)/3600)+"小时":"");
    else if(second<86400*365)
        return Math.floor(second/86400/30)+"月"+(second%(86400*30)!=0?Math.floor((second%(86400*30))/86400)+"天":"");
    else
        return Math.floor(second/86400/365)+"年"+(second%(86400*365)!=0?Math.floor((second%(86400*365))/(86400*30))+"月":"");
}


//判断是否是json
function isJSON(str,isarray=true){
    if(typeof str == 'string'){
        try{
            let obj = JSON.parse(str);
            if(typeof obj == 'object' && obj){
                return obj;
            }else{
                if(isarray){
                    return [];
                }
                return {};
            }
        }catch(e){
            if(isarray){
                return [];
            }
            return {};
        }
    }else{
        if(isarray){
            return [];
        }
        return {};
    }
}

//把解码后的json打印出来，有的时候页面打开后需要提示一些信息，比如页面跳转后，我们还需要提示信息
function echoMsg(objName,result,time=5000,jump=true){
    if(!result){
        result={
            status: 4,
            message: "数据传输错误！"
        }
    }
    let obj=$('body').find(objName);
    if(result.status===1&&result.message!==""){
        obj.append('<div class="alert alert-success alert-dismissible" role="alert"><i class="bi bi-check-circle-fill"></i> '+result.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        window.setTimeout(function(){$(".alert-dismissible").remove();},time);
    }else if(result.status===2){
        obj.append('<div class="alert alert-info alert-dismissible" style="width: 100%;z-index: 1000;top: 0;" role="alert"><i class="bi bi-info-circle-fill"></i> '+result.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        window.setTimeout(function(){$(".alert-dismissible").remove();},time);
    }else if(result.status===3){
        obj.append('<div class="alert alert-warning" role="alert"><i class="bi bi-exclamation-circle-fill"></i> '+result.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
    }else if(result.status===4){
        obj.append('<div class="alert alert-danger alert-dismissible" role="alert" style="width: 100%;z-index: 1000;top: 0;" ><i class="bi bi-exclamation-triangle-fill"></i> '+result.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        window.setTimeout(function(){$(".alert-dismissible").remove();},time);
    }
    if(jump===true && 'url' in result && result.url!==null){
        window.location.href=result.url;
    }
    return result.status;
}

function startload(){
    $('#loading').show();
}
function stopload(){
    $('#loading').hide();
}
function failload(){
    setTimeout(stopload,2500);
}


/**
 * 设置select控件选中
 * @param selectId select的id值
 * @param checkValue 选中option的值
 */
function select_option_checked(selectId, checkValue){
    let select = document.getElementById(selectId);
    for (let i = 0; i < select.options.length; i++){
        if (select.options[i].value === checkValue){
            select.options[i].selected = true;
            return true;
        }
    }
    return false;
}

function initmodal(id) {
    let modal=document.getElementById(id);
    modal.querySelector('.modal-title').textContent="";
    modal.querySelector('.modal-body').textContent="";
    // let msg=document.createElement('div');
    // msg.id=id+"-msg";
    // msg.style="z-index:2000;position:absolute;width: 100%;top:0;";
    // modal.querySelector('.modal-body').appendChild(msg);
}

//获取日期
function getDate(datestr){
    return new Date(datestr.replace(/-/g,"/"));
}
//转化为日期
function toDatetime(datestr){
    const date=new Date(datestr);
    const y=date.getFullYear();
    const m=date.getMonth()+1;
    const d=date.getDate();
    return y+"-"+(m<10?"0"+m:m)+"-"+(d<10?"0"+d:d)+"T"+(date.toTimeString()).substring(0,8);
}
function toDate(datestr){
    const date=new Date(datestr);
    const y=date.getFullYear();
    const m=date.getMonth()+1;
    const d=date.getDate();
    return y+"-"+(m<10?"0"+m:m)+"-"+(d<10?"0"+d:d);
}
function url2json(url){
    let params={};
    if(url.includes('?')){
        let arr=url.replace(/.*\?/,'');
        arr=arr.split('&');
        for(item of arr){
            if(item.includes('=')){
                tem=item.split('=');
                params[tem[0]]=decodeURI('1' in tem ? tem[1]:null);
            }
        }
    }
    return params;
}
function setParams(params){
    let urlparams=url2json(window.location.href);
    for(i in params){
        if(i in urlparams){
            params[i]=urlparams[i];
        }
    }
}
function json2url(json){
    let url="";
    for(i in json){
        url+=i+"="+encodeURI(json[i])+"&";
    }
    if(url.length>0)
        url[url.length]='';
    return url;
}
//分页函数
function setpaginator(app){
    app.paginator.pre=app.paginator.current_page>app.pagenum-1?app.paginator.current_page-app.pagenum+1:1;
    app.paginator.next=app.paginator.pre+app.pagenum*2-2<app.paginator.last_page?app.paginator.pre+app.pagenum*2-2:app.paginator.last_page;
    // if(app.paginator.pre-app.pagenum>0){
    //     app.paginator.pre_url=app.paginator.first_page_url.replace(/page=1/,"page="+(app.paginator.pre-app.pagenum));
    // }
    // if(app.paginator.next+app.pagenum<app.paginator.last_page){
    //     app.paginator.next_url=app.paginator.first_page_url.replace(/page=1/,"page="+(app.paginator.next+app.pagenum));
    // }
    // while(i<=app.paginator.next){
    //     app.paginator.urllist.push({url:app.paginator.first_page_url.replace(/page=1/,"page="+i),page:i});
    //     i++;
    // }
    app.paginator.pagelist=[];
    let i=app.paginator.pre;
    while(i<=app.paginator.next){
        app.paginator.pagelist.push(i);
        i++;
    }
    if(app.dataname==='notices'){
        app.initStatus();
    }
}
function initaddress(app,sla,params){
    const city_id=params.city_id;
    const region_id=params.region_id;
    getData(sla+"?type=s&id=44",function(json){
        app.state_ids=json;
    });
    app.getRegions=function(region_id=""){
        getData(sla+"?type=r&id="+params.city_id,function(json){
            app.region_ids=json;
            if(region_id!==""){
                params.region_id=region_id;
            }else{
                params.region_id="";
            }
        });
    };
    app.getCities=function(city_id="",region_id=""){
        getData(sla+"?type=c&id="+params.state_id,function(json){
            app.city_ids=json;
            if(city_id===""){
                params.city_id="";
                app.region_ids=[];
            }else{
                params.city_id=city_id;
                if(region_id!==""){
                    app.getRegions(region_id);
                }else{
                    params.region_id="";
                }
            }
        });
    };
    app.getCities(city_id,region_id);
}

//初始化标页码
function initpaginator(app){
    setParams(app.params);//初始化参数
    app.getData=function(params=app.params){
        let url='?'+json2url(params);
        history.replaceState(null,null,window.location.href.replace(/\?.*/,'')+url);
        getData(app.url+url,function(json){
            app.paramspre=Object.assign({},params);
            if(json.data!==null){
                // if(app.dataname==='stations'){
                //     json.data.stations.data.forEach(station=>{
                //         station.sinfo=isJSON(station.sinfo);
                //         station.service=(station.sinfo.p===1?'<span class="badge bg-info">核酸</span>':'')+(station.sinfo.r===1?'<span class="badge bg-warning">抗原</span>':'')+(station.sinfo.v===1?'<span class="badge bg-success">疫苗</span>':'');
                //     });
                // }
                app.data=json.data;
                app.paginator=json.data[app.dataname];
                app[app.dataname]=json.data[app.dataname].data;
                setpaginator(app);
                if(app.dataname==='stations'){
                }
            }else{
                app[app.dataname]=[];
            }
        },"#msg");
    };
    app.setpage=function (page) {
        app.paramspre.page=page;
        app.getData(app.paramspre);
    };
    app.orderby=function (by) {
        app.params.order=by;
        app.getData();
    };
    app.setdesc=function () {
        app.params.desc=(app.params.desc==='0'?'1':'0');
        app.getData();
    };
    app.settype=function (type) {
        app.params.type=type;
        app.getData();
    };
}


function filterTypes(types,typekey){
    for(index in types)
        if(!typekey.includes(index))
            delete types[index];
}
function getTags(json,tags,tags0=[]){
    tags.length=0;
    tags0.length=0;
    if('tags' in json){
        json.tags.forEach(v=>tags0.push(v));
        for(let i in json.tags){
            tags[json.tags[i].tid]={tname:json.tags[i].tname,tdes:json.tags[i].tdes,tnum:json.tags[i].tnum};
        }
    }
}

//前端发送的ajax请求
//把get的请求和post包装在一个函数里
//数据data不为空就走$.post
//$.post 和$.get都是jquery内置的发送ajax请求的函数
//参数url是请求地址,参数f是回调函数，简单来说就是js请求地址获得数据后我们要进行的操作都可以写在这个f函数里
//参数echo是消息需要展示在页面的哪个位置，用dom节点的id定位
//jump是是否跳转，load是否显示加载动画，time是显示时间

//Post中，第一个参数就是地址，我们将这个getData函数的参数url传入其中。第二个参数是数据，也是将参数data传入。第三个是回调函数，请求后的数据会自动放在result中，我们就是要对result进行处理。
//我们通过isJSON对这个字符串result解码为json对象
//最后调用echoMsg显示消息，调用传进来的f函数进行后续处理
function getData(url,f=null,echo=false,data=null,jump=true,load=true,time=5000){
    if(load)    //显示转圈圈
        startload();
    if(data===null){
        $.get(url,function(result){
            let json=isJSON(result);
            if(echo!==false){
                echoMsg(echo,json,time,jump);
            }
            if(f!==null)
                f(json);
            if(load)
                stopload();
        }).fail(function(xhr, status, info){
            let result={
                status: 4,
                message: ""
            }
            if(xhr.status == 419){
                result.message="页面过期，请刷新后重试！";
                echoMsg(echo,result,time,jump);
            }else if(xhr.status == 404){
                result.message="请求链接失效！";
                echoMsg(echo,result,time,jump);
            }else{
                result.message="服务器错误，请稍后重试！";
                echoMsg(echo,result,time,jump);
            }
        });
    }else{
        $.post(url,data,function(result){
            let json=isJSON(result);
            if(echo!==false){
                echoMsg(echo,json,time,jump);
            }
            if(f!==null)
                f(json);
            if(load)
                stopload();
        }).error(function(xhr, status, info){
            let result={
                status: 4,
                message: ""
            }
            if(xhr.status == 419){
                result.message="页面过期，请刷新后重试！";
                echoMsg(echo,result,time,jump);
            }else if(xhr.status == 404){
                result.message="请求链接失效！";
                echoMsg(echo,result,time,jump);
            }else{
                result.message="服务器错误，请稍后重试！";
                echoMsg(echo,result,time,jump);
            }
        });
    }
    if(load)
        failload();
}

