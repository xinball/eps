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

function copy(obj){
    if(obj.value!==""){
        $(obj).select();
        document.execCommand('copy');
        echoMsg('#msg',{status:1,message:"复制成功！"});
    }
}
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
    if(typeof str === 'string'){
        try{
            let obj = JSON.parse(str);
            if(typeof obj === 'object' && obj){
                return obj;
            }else{
                return null;
                // if(isarray){
                //     return [];
                // }
                // return {};
            }
        }catch(e){
            return null;
            // if(isarray){
            //     return [];
            // }
            // return {};
        }
    }else{
        return null;
        // if(isarray){
        //     return [];
        // }
        // return {};
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
function getDayTime(time){
    return (new Date(new Date().toLocaleDateString()+' '+time).getTime()-new Date(new Date().toLocaleDateString()).getTime());
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
                if(params[tem[0]]!==null){
                    const json = isJSON(params[tem[0]]);
                    if(json!==null){
                        params[tem[0]]=json;
                    }
                }
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
        if(typeof json[i] === 'object'){
            url+=i+"="+encodeURI(JSON.stringify(json[i]))+"&";
        }else{
            url+=i+"="+encodeURI(json[i])+"&";
        }
    }
    if(url.length>0)
        url[url.length]='';
    return url;
}
function initStatus(app,timename,before=7,length=1){
    if(app.time[timename+'timer']!==undefined){
        clearInterval(app.time[timename+'timer']);
    }
    app.time[timename+'before']=before*86400000;
    app.time[timename+'length']=length*1000;
    app.time[timename+'timer']=null;
    app.time[timename]=[];
    for(i in app[app.dataname]){
        const item=app[app.dataname][i];
        app.time[timename][i]={
            //操作时间距今多久
            length:(new Date()).getTime()-getDate(item[timename]).getTime(),
            status:""
        };
        getStatus(item[timename],app.time[timename][i],app.time[timename+'before'])
    }
    app.time[timename+'timer']=setInterval(() => {
        refreshStatus(app,timename);
    }, app.time[timename+'length']);
}
//超过before（7天）就显示日期
//没超过7天就显示多少天多少小时前
function getStatus(date,send,before){
    if(send.length!==null){
        //大于7天
        if(send.length>before){
            send.length=null;
            send.status=date;
        }else if(send.length>=0){
            //小于7天显示时间
            send.status=getSubTime(0,send.length);
        }else{
            send.length=null;
            send.status="未生效";
        }
    }
}
function refreshStatus(app,timename){
    for(i in app[app.dataname]){
        const item=app[app.dataname][i];
        if(app.time[timename][i].length!==null){
            app.time[timename][i].length+=app.time[timename+'length'];
            getStatus(item[timename],app.time[timename][i],app.time[timename+'before']);
        }
    }
}

//分页函数
function setpaginator(app){
    app.paginator.pre=app.paginator.current_page>app.pagenum-1?app.paginator.current_page-app.pagenum+1:1;
    app.paginator.next=app.paginator.pre+app.pagenum*2-2<app.paginator.last_page?app.paginator.pre+app.pagenum*2-2:app.paginator.last_page;
    
    app.paginator.pagelist=[];
    let i=app.paginator.pre;
    while(i<=app.paginator.next){
        app.paginator.pagelist.push(i);
        i++;
    }
}
function initaddress(app,sla,params,counflag=0){
    const coun_id=params.coun_id===null?"":params.coun_id;
    const state_id=params.state_id===null?"":params.state_id;
    const city_id=params.city_id===null?"":params.city_id;
    const region_id=params.region_id===null?"":params.region_id;
    app.getStreet=function(e=0){
        if(e===1){
            search(app,app.region_ids,"district",params.region_id);
        }
    };
    app.getRegions=function(region_id="",e=0){
        getData(sla+"?type=r&id="+params.city_id,function(json){
            app.region_ids=json;
            params.region_id=region_id;
            app.getStreet(e);
            if(e===1){
                search(app,app.city_ids,"city",params.city_id);
            }
        });
    };
    app.getCities=function(city_id="",region_id="",e=0){
        getData(sla+"?type=c&id="+params.state_id,function(json){
            app.city_ids=json;
            params.city_id=city_id;
            if(city_id===""){
                app.region_ids=[];
            }else{
                app.getRegions(region_id,e);
            }
            if(e===1){
                search(app,app.state_ids,"province",params.state_id);
            }
        });
    };
    if(counflag){
        app.getStates=function(state_id="",city_id="",region_id=""){
            getData(sla+"?type=s&id="+params.coun_id,function(json){
                app.state_ids=json;
                params.state_id=state_id;
                if(state_id===""){
                    app.city_ids=[];
                }else{
                    app.getCities(city_id,region_id);
                }
            });
        };
        app.getCountries=function(coun_id="",state_id="",city_id="",region_id=""){
            getData(sla+"?type=g&id="+params.con_id,function(json){
                app.coun_ids=json;
                params.coun_id=coun_id;
                if(coun_id===""){
                    app.states_ids=[];
                }else{
                    app.getStates(state_id,city_id,region_id);
                }
            });
        };
        getData(sla+"?type=z",function(json){
            app.con_ids=json;
            app.getCountries(coun_id,state_id,city_id,region_id);
        });
    }else{
        getData(sla+"?type=s&id=44",function(json){
            app.state_ids=json;
            app.getCities(city_id,region_id,1);
        });
    }
}

//初始化标页码
function initpaginator(app){
    setParams(app.params);//初始化参数
    app.paramspre=Object.assign({},app.params);
    app.getData=function(params=app.params){
        let url='?'+json2url(params);
        history.replaceState(null,null,window.location.href.replace(/\?.*/,'')+url);
        if(params.order !== undefined && app.ordertypes[params.order] !== undefined){
            echoMsg("#msg",{status:2,message:"查询方式："+app.ordertypes[params.order]+"-"+(params.desc==='0'?"正序":"倒序")});
        }
        getData(app.url+url,function(json){
            if(json.data!==null){
                app.data=json.data;
                app.paginator=json.data[app.dataname];
                app[app.dataname]=json.data[app.dataname].data;
                setpaginator(app);
                if(app.dataname==='operations'){
                    for(let i in app[app.dataname]){
                        app[app.dataname][i].orequest=isJSON(app[app.dataname][i].orequest);
                        app[app.dataname][i].oinfo=isJSON(app[app.dataname][i].oinfo);
                        app[app.dataname][i].oresult=isJSON(app[app.dataname][i].oresult);
                    }
                }
                if(app.dataname==='stations'){
                    for(let i of app.markers){
                        clearMarker(i);
                    }
                    app.markers.length=0;
                    for(let i of app[app.dataname]){
                        app.markers.push(addMarker(i,app.mapObj));
                    }
                }
                if(app.dataname==='notices'||app.dataname==='operations'){
                    app.initStatus();
                }
            }else{
                app[app.dataname]=[];
            }
        },"#msg");
    };
    app.setpage=function (page) {
        app.paramspre=Object.assign({},app.params);
        app.paramspre.page=page;
        app.getData(app.paramspre);
    };
    app.set=function(key,value=""){
        if(key==='desc'){
            app.params.desc=(app.params.desc==='0'?'1':'0');
        }else if(key==='order'){
            if(app.params.order===value){
                app.params.desc=(app.params.desc==='0'?'1':'0');
            }else{
                app.params[key]=value;
            }
        }else{
            app.params[key]=value;
        }
        app.getData();
    }
    app.setarr=function(key,value=""){
        if(app.params[key].includes(value)){
            const index = app.params[key].indexOf(value);
            app.params[key].splice(index,1);
        }else{
            app.params[key].push(value);
        }
        app.getData();
    }
}

function getStation(station){
    if(!('approvetime' in station.sinfo)){
        station.sinfo.approvetime=false;
    }else{
        station.sinfo.approvetime=station.sinfo.approvetime==='1';
    }
    if(!('a' in station.sinfo)){
        station.sinfo.a=false;
    }else{
        station.sinfo.a=station.sinfo.a==='1';
    }
    if(!('p' in station.sinfo)){
        station.sinfo.p=false;
    }else{
        station.sinfo.p=station.sinfo.p==='1';
    }
    if(!('r' in station.sinfo)){
        station.sinfo.r=false;
    }else{
        station.sinfo.r=station.sinfo.r==='1';
    }
    if(!('v' in station.sinfo)){
        station.sinfo.v=false;
    }else{
        station.sinfo.v=station.sinfo.v==='1';
    }
    if(!('des' in station.sinfo)){
        station.sinfo.des="";
    }
    if(!('addr' in station.sinfo)){
        station.sinfo.addr="";
    }
    if(!('time' in station.sinfo)){
        station.sinfo.time="";
    }
    if(!('anum' in station.sinfo)){
        station.sinfo.anum=0;
    }
    if(!('pnum' in station.sinfo)){
        station.sinfo.pnum=0;
    }
    if(!('rnum' in station.sinfo)){
        station.sinfo.rnum=0;
    }
    if(!('vnum' in station.sinfo)){
        station.sinfo.vnum=0;
    }
    return station;
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
                stopload();
            }else if(xhr.status == 404){
                result.message="请求链接无效！";
                echoMsg(echo,result,time,jump);
                stopload();
            }else{
                result.message="服务器错误，请稍后重试！";
                echoMsg(echo,result,time,jump);
                stopload();
            }
            if(f!==null)
                f(result);
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
                stopload();
            }else if(xhr.status == 404){
                result.message="请求链接失效！";
                echoMsg(echo,result,time,jump);
                stopload();
            }else{
                result.message="服务器错误，请稍后重试！";
                echoMsg(echo,result,time,jump);
                stopload();
            }
            if(f!==null)
                f(result);
        });
    }
    if(load)
        failload();
}


function getPolygon(app,data) {
    if(data===undefined||data.boundaries===undefined)
        return;
    var bounds = data.boundaries;
    if (bounds) {
        for (var i = 0, l = bounds.length; i < l; i++) {
            var polygon = new AMap.Polygon({
                map: app.mapObj,
                strokeWeight: 1,
                strokeColor: '#0091ea',
                fillColor: '#80d8ff',
                fillOpacity: 0.2,
                path: bounds[i]
            });
            app.polygons.push(polygon);
        }
        app.mapObj.setFitView();//地图自适应
    }
}
function search(app,datalist,level,id) {
    if(datalist===null||app.mapObj===undefined)
        return;
    //清除地图上所有覆盖物
    for (var i = 0, l = app.polygons.length; i < l; i++) {
        app.polygons[i].setMap(null);
    }
    let adcode = null;
    for(let i of datalist){
        if(i.id==id){
            adcode=i.code;
            break;
        }
    }
    if(adcode===null)
        return;
    app.district.setLevel(level); //行政区级别
    app.district.setExtensions('all');
    app.district.search(adcode, function(status, result) {
        if(status === 'complete'){
            getPolygon(app,result.districtList[0]);
        }
    });
}
function setCenter(obj){
    map.setCenter(obj[obj.options.selectedIndex].center)
}
function addMarker(station,map) {
    let marker = new AMap.Marker({
        position: [station.slng, station.slat],
        offset: new AMap.Pixel(-13, -30)
    });
    let markerContent = document.createElement("div");
    let markerImg = document.createElement("img");
    markerImg.className = "markerlnglat";
    markerImg.src = "//eps.yono.top/bootstrap/icon/geo-fill.svg";
    markerImg.setAttribute('width', '25px');
    markerImg.setAttribute('height', '34px');
    let markerSpan = document.createElement("span");
    markerSpan.className = 'card';
    markerSpan.style = 'width: max-content';
    markerSpan.innerHTML = station.sid+" "+station.sname;
    markerContent.appendChild(markerSpan);
    markerContent.appendChild(markerImg);
    marker.setContent(markerContent); //更新点标记内容
    marker.setMap(map);
    return marker;
}
function clearMarker(marker) {
    if (marker) {
        marker.setMap(null);
        marker = null;
    }
}