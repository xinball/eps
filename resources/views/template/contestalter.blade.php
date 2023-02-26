
<x-modal id="alter" class="modal-fullscreen ckeditor" title="编辑比赛@@{{ contest.ctitle===''?'':'-'+contest.ctitle }}">
    <div class="text-center p-4 pb-4">
        <div class="crop-contest" id="crop-contest">
            <div class="contest-view btn btn-outline-dark" style="cursor: pointer;" title="更换比赛图片">
                <img  :src="contest.img" alt="比赛图片" height="72" width="72" style="font-size: 68px;border-radius: 8px;" >
            </div>
        </div>
        <h4 class="mb-3">基本信息</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">ID</label>
                <input type="text" class="form-control" v-model="contest.cid" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">参赛人数</label>
                <div class="input-group">
                    <input type="text" class="form-control" v-model="contest.cnum" disabled>
                    <a class="btn btn-outline-danger" @click="clearuser">清空所有参赛用户</a>
                </div>
            </div>
            <div class="col-md-6">
                <label for="alterctitle" class="form-label">标题</label>
                <input type="text" class="form-control" id="alterctitle" v-model="contest.ctitle" placeholder="比赛标题" required>
            </div>
            <div class="col-md-6">
                <label for="altercdes" class="form-label">描述</label>
                <input type="text" class="form-control" id="altercdes" v-model="contest.cdes" placeholder="比赛描述" required>
            </div>
            <div class="col-md-6">
                <label for="altercstart" class="form-label">开始时间</label>
                <input type="datetime-local" class="form-control" id="altercstart" v-model="contest.cstart" placeholder="开始时间" required>
            </div>
            <div class="col-md-6">
                <label for="altercend" class="form-label">结束时间</label>
                <input type="datetime-local" class="form-control" id="altercend" v-model="contest.cend" placeholder="结束时间" required>
            </div>
        </div>
        <hr class="my-4"><br><br>
        <h4 class="mb-3">选项配置</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="alterctype" class="form-label">类型</label>
                <select class="form-select" v-model="contest.ctype" id="alterctype" required>
                    <option v-for="(ctype,index) in ctypes"  :key="index" :label="ctype" :value="index">@{{ ctype }}</option>
                </select>
            </div>
            <div v-if="typekey.o.includes(contest.ctype)" class="col-md-6">
                <label for="alterpwd" class="form-label">密码</label>
                <input type="text" class="form-control" id="alterpwd" v-model="contest.coption.pwd" placeholder="密码【不填写则不设置】" required>
            </div>
            <div v-if="typekey.s.includes(contest.ctype)"  class="col-md-6">
                <label for="alterusersource" class="form-label">参与比赛用户来源</label>
                <select class="form-select" v-model="usersource" id="alterusersource" required>
                    <option v-for="(usersource,index) in usersources" :key="index" :label="usersource" :value="index">@{{ usersource }}</option>
                </select>
            </div>
            <div v-if="typekey.s.includes(contest.ctype)&&usersource==='f'" class="col-md-6">
                <label for="alterfilepath" class="form-label">用户名/用户编号序列文件 <i class="bi bi-filetype-csv"></i>【不上传表示暂时不添加用户】</label>
                <input type="file" class="form-control" @change="getuserlist($event)" accept=".csv" id="alterfilepath" required>
            </div>
            <div v-if="typekey.s.includes(contest.ctype)&&usersource==='c'" class="col-md-6">
                <label for="alterprefix" class="form-label">用户前缀</label>
                <input type="text" class="form-control" v-model="prefix" id="alterprefix" placeholder="用户名前缀" required>
            </div>
            <div v-if="typekey.s.includes(contest.ctype)&&usersource==='c'" class="col-md-6">
                <label for="alterstartnum" class="form-label">起始编号</label>
                <input type="number" class="form-control" v-model="startnum" id="alterstartnum" placeholder="起始编号" required>
            </div>
            <div v-if="typekey.s.includes(contest.ctype)&&usersource==='c'" class="col-md-6">
                <label for="alterendnum" class="form-label">结束编号</label>
                <input type="number" class="form-control" v-model="endnum" id="alterendnum" placeholder="结束编号" required>
            </div>
            <div id="ipdis" class="col-md-12">
                <label for="alterip" class="form-label">允许IP范围
                    <button type="button" class="btn btn-outline-info" @click="insertip"><i class="bi bi-plus-lg"></i></button>
                </label>
                <div class="input-group" v-for="(ip,index) in contest.coption.ips" :key="index">
                    <input type="text" class="form-control" v-model="ip.start" placeholder="起始IP段【如：192.168.1.1】" required>
                    <input type="text" class="form-control"  v-model="ip.end" placeholder="结束IP段【如：192.168.1.10】" required>
                    <button type="button" class="btn btn-outline-danger" @click="delip(index)"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
        <hr class="my-4"><br><br>
        <h4 class="mb-3">规则配置</h4>
        <div class="row g-3">
            <div class="col-md-6 text-center">
                <div class="form-check form-check-inline">
                    <input type="radio" name="alterrule" class="form-check-input" id="alterrulea" v-model="contest.coption.rule" value="a" required>
                    <label for="ralterrulea" class="form-check-label">ACM</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="alterrule" class="form-check-input" id="alterrulei" v-model="contest.coption.rule" value="i" required>
                    <label for="alterrule" class="form-check-label">IOI</label>
                    <a href="http://oj.maythorn.top/notice/13" target="_blank" class="btn btn-outline-info">赛制介绍</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch form-check-inline">
                    <label for="alterrtrank" class="form-check-label">实时排名</label>
                    <input type="checkbox" class="form-check-input" id="alterrtrank" v-model="contest.coption.rtrank" placeholder="实时排名" required>
                </div>
            </div>
            <div class="col-md-6">
                <label for="alternumlimit" class="form-label">提交次数限制【不填写表示无限制】</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="alternumlimit" v-model="contest.coption.numlimit" placeholder="提交次数限制【不填写表示无限制】" required>
                </div>
            </div>
            <div v-if="contest.coption.rule==='a'" class="col-md-6">
                <label for="alterpunish" class="form-label">罚时</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="alterpunish" v-model="contest.coption.punish" placeholder="罚时" required>
                    <span class="input-group-text">分钟</span>
                </div>
            </div>
        </div>
        <hr class="my-4"><br><br>
        <h4 class="mb-3">详细描述</h4>
        <textarea id="altereditor" class="ckeditor"></textarea>
        <hr class="my-4"><br><br>
        <h4 class="mb-3">题目<div class="input-group"><input class="form-control" type="number" v-model="pid" placeholder="请输入问题编号"> <button type="button" class="btn btn-outline-info" @click="insertpid"><i class="bi bi-plus-lg"></i> 添加题目</button></div></h4>
        <div class="row g-3">
            <div class="col-md-12 input-group" v-for="(pid,index) in contest.pids" :key="index">
                <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" :href="'/problem/'+pid">@{{ "#"+pid+" "+contest.problems[index].ptitle+" "+contest.problems[index].pdes }}</a>
                <button type="button" class="btn btn-outline-danger" @click="delpid(index)"><i class="bi bi-x-lg"></i></button>
                <button type="button" class="btn btn-outline-info" @click="uppid(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                <button type="button" class="btn btn-outline-secondary" @click="downpid(index)" :disabled="index===contest.pids.length-1"><i class="bi bi-arrow-down"></i></button>
            </div>
        </div>
        <hr class="my-4"><br><br>
        <h4 class="mb-3">比赛管理员<div class="input-group"><input class="form-control" type="number" v-model="uid" placeholder="请输入用户名/邮箱/ID"> <button type="button" class="btn btn-outline-info" @click="insertuid"><i class="bi bi-plus-lg"></i> 添加管理员</button></div></h4>
        <div class="row g-3">
            <div class="col-md-12 input-group" v-for="(uid,index) in contest.auids" :key="index">
                <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" :href="'/user/'+uid">@{{ "#"+uid+" "+contest.ausers[index].uname+" "+contest.ausers[index].uemail }}</a>
                <button type="button" class="btn btn-outline-danger" @click="deluid(index)"><i class="bi bi-x-lg"></i></button>
                <button type="button" class="btn btn-outline-info" @click="upuid(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                <button type="button" class="btn btn-outline-secondary" @click="downuid(index)" :disabled="index===contest.auids.length-1"><i class="bi bi-arrow-down"></i></button>
            </div>
        </div>
    </div>
    <x-slot name="footer">
        <div v-show="file!=null" class="progress" style="width: 200px">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{width:fileprogress + '%'}" ></div>
        </div>
        <button type="button" class="btn btn-outline-success"  @click="alter">修改</button>
    </x-slot>

<x-modal id="contest-modal" title="更换比赛图片" class="modal-fullscreen">
    <form id="uploadcontest" class="avatar-form" :action="'{!! config('var.cua') !!}'+contest.cid" enctype="multipart/form-data" method="post">
        {{ csrf_field() }}
        <div class="avatar-body">
            <!-- Upload image and data -->
            <div class="avatar-upload">
                <input class="avatar-src" name="avatar_src" type="hidden">
                <input class="avatar-src" name="cid" :value="contest.cid" type="hidden">
                <input class="avatar-data" name="avatar_data" type="hidden">
                <label for="avatarInput">本地上传</label>
                <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
            </div>

            <!-- Crop and preview -->
            <div class="row">
                <div class="col-md-9">
                    <div class="avatar-wrapper"></div>
                </div>
                <div class="col-md-3">
                    <div class="avatar-preview preview-avatar-lg"></div>
                    <div class="avatar-preview preview-avatar-md"></div>
                    <div class="avatar-preview preview-avatar-sm"></div>
                </div>
            </div>
            <x-slot name="footer">
                <div class="row avatar-btns">
                    <div class="col-md-9">
                        <div class="btn-group">
                            <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees">-90°</button>
                            <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-15" type="button">-15°</button>
                            <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-30" type="button">-30°</button>
                            <button class="btn btn-outline-danger btn-like" data-method="rotate" data-option="-45" type="button">-45°</button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-outline-info" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees">+90°</button>
                            <button class="btn btn-outline-info" data-method="rotate" data-option="15" type="button">15°</button>
                            <button class="btn btn-outline-info" data-method="rotate" data-option="30" type="button">30°</button>
                            <button class="btn btn-outline-info" data-method="rotate" data-option="45" type="button">45°</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button form="uploadcontest"  class="btn btn-outline-success btn-block avatar-save" type="submit">保存</button>
                    </div>
                </div>
            </x-slot>
        </div>
    </form>
</x-modal>
</x-modal>

    <script>
        let altereditor;
        const alterapp=Vue.createApp({
            data() {
                return{
                    contest:{
                        ctitle: "",
                        cdes: "",
                        ctype: "o",
                        cstart: "",
                        cend: "",
                        cpwd: "",
                        cinfo: "",
                        pids:[],
                        problems: [],
                        auids:[],
                        ausers: [],
                        coption:{
                            rule: "a",
                            pwd: "",
                            punish:"10",
                            rtrank:true,
                            numlimit:"",
                            ips:[],
                        },
                    },

                    prefix: "",
                    startnum: "",
                    endnum: "",
                    usersource: "c",
                    
                    rule: "",
                    // ips: [
                    //     {start:"",end:""}
                    // ],
                    // pids: [],

                    pid:"",
                    uid:"",
                    file: null,
                    fileprogress: 0,
                    ctypes:isJSON({!! json_encode($config_contest['type'],JSON_UNESCAPED_UNICODE) !!}),
                    usersources:isJSON({!! json_encode($config_contest['usersource'],JSON_UNESCAPED_UNICODE) !!}),
                    typekey:{!! json_encode($config_contest['typekey']) !!},
                }
            },
            mounted(){
                this.init();
            },
            methods:{
                getuserlist(event){
                    this.file=event.currentTarget.files[0];
                    this.fileprogress=0;
                },
                insertip(){
                    this.contest.coption.ips.push({start:"",end:""});
                    // alert(JSON.stringify(this.contest.coption.ips));
                },
                delip(index){
                    this.contest.coption.ips.splice(index,1);
                },
                insertpid(){
                    if(!this.contest.pids.includes(this.pid)){
                        let that=this;
                        getData("{!! isset($utype)&&$utype=='a'?config('var.apg'):config('var.pg') !!}"+this.pid,
                        function(json){
                            if(json.data!==null){
                                if(!'problem' in json.data||!'ptype' in json.data.problem){
                                    echoMsg("#alter-msg",{status:4,message:"查询不到该编号对应的问题数据！"});
                                }else if(json.data.problem.ptype!=='m'){
                                    echoMsg("#alter-msg",{status:4,message:"该问题类型不为可加入比赛类型，无法添加！"});
                                }else{
                                    that.contest.pids.push(that.pid);
                                    that.contest.problems.push(json.data.problem);
                                }
                            }
                        },"#alter-msg",null,false);
                    }else{
                        echoMsg("#alter-msg",{status:4,message:"该问题已添加"});
                    }
                },
                delpid(index){
                    this.contest.pids.splice(index,1);
                    this.contest.problems.splice(index,1);
                },
                uppid(index){
                    if(index>0){
                        let tem=this.contest.pids[index];
                        this.contest.pids[index]=this.contest.pids[index-1];
                        this.contest.pids[index-1]=tem;
                        let temproblem=this.contest.problems[index];
                        this.contest.problems[index]=this.contest.problems[index-1];
                        this.contest.problems[index-1]=temproblem;
                    }
                },
                downpid(index){
                    if(index<this.contest.pids.length-1){
                        let tem=this.contest.pids[index];
                        this.contest.pids[index]=this.contest.pids[index+1];
                        this.contest.pids[index+1]=tem;
                        let temproblem=this.contest.problems[index];
                        this.contest.problems[index]=this.contest.problems[index+1];
                        this.contest.problems[index+1]=temproblem;
                    }
                },

                insertuid(){
                    if(!this.contest.auids.includes(this.uid)){
                        let that=this;
                        getData("{!! isset($utype)&&$utype=='a'?config('var.aug'):config('var.ug') !!}"+this.uid,
                        function(json){
                            if(json.data!==null){
                                if(!'user' in json.data){
                                    echoMsg("#alter-msg",{status:4,message:"查询不到该编号对应的用户数据！"});
                                }else{
                                    that.contest.auids.push(that.uid);
                                    that.contest.ausers.push(json.data.user);
                                }
                            }
                        },"#alter-msg",null,false);
                    }else{
                        echoMsg("#alter-msg",{status:4,message:"该用户已添加"});
                    }
                },
                deluid(index){
                    this.contest.auids.splice(index,1);
                    this.contest.ausers.splice(index,1);
                },

                upuid(index){
                    if(index>0){
                        let tem=this.contest.auids[index];
                        this.contest.auids[index]=this.contest.auids[index-1];
                        this.contest.auids[index-1]=tem;
                        let temproblem=this.contest.ausers[index];
                        this.contest.ausers[index]=this.contest.ausers[index-1];
                        this.contest.ausers[index-1]=temproblem;
                    }
                },
                downuid(index){
                    if(index<this.contest.auids.length-1){
                        let tem=this.contest.auids[index];
                        this.contest.auids[index]=this.contest.auids[index+1];
                        this.contest.auids[index+1]=tem;
                        let temproblem=this.contest.ausers[index];
                        this.contest.ausers[index]=this.contest.ausers[index+1];
                        this.contest.ausers[index+1]=temproblem;
                    }
                },
                clearuser(){
                    getData('{!! config('var.cuc') !!}'+this.contest.cid,null,"#alter-msg");
                },
                alter(){
                    let data={
                        ctitle:this.contest.ctitle,
                        cdes:this.contest.cdes,
                        ctype:this.contest.ctype,
                        cstart:this.contest.cstart,
                        cend:this.contest.cend,
                        cinfo:altereditor.getData(),
                        pids:JSON.stringify(this.contest.pids),
                        auids:JSON.stringify(this.contest.auids),
                        ips:JSON.stringify(this.contest.coption.ips),
                        rule:this.contest.coption.rule,
                        pwd:this.contest.coption.pwd,
                        punish:this.contest.coption.punish,
                        rtrank:this.contest.coption.rtrank,
                        numlimit:this.contest.coption.numlimit,
                        utype:"{{ $utype }}",
                        _token:"{{csrf_token()}}"
                    };
                    if(this.typekey.s.includes(this.contest.ctype)){
                        data.pwd="";
                        data.usersource=this.usersource;
                        if(this.usersource==='c'){
                            data.prefix=this.prefix;
                            data.startnum=this.startnum;
                            data.endnum=this.endnum;
                            getData("{!! config('var.ca') !!}"+this.contest.cid,null,"#alter-msg",data);
                        }else{
                            let that=this;
                            that.fileprogress=0;
                            try {
                                if(this.file===null){
                                    getData("{!! config('var.ca') !!}"+this.contest.cid,null,"#alter-msg",data);
                                    // echoMsg("#alter-msg",{status:4,message:"请选择要上传的文件！"});
                                    return;
                                }
                                const total=this.file.size;
                                if(total>10240){
                                    echoMsg("#alter-msg",{status:4,message:"文件大小不得大于10KB！"});
                                    return;
                                }
                                let reader = new FileReader();
                                reader.readAsText(this.file);
                                let loaded=0;
                                reader.onprogress=function (e) {
                                    loaded+=e.loaded;
                                    that.fileprogress=(loaded/total)*100
                                }
                                reader.onload=function () {
                                    if(reader.result){
                                        data.userlist=reader.result;
                                        console.log(data);
                                        getData("{!! config('var.ca') !!}"+that.contest.cid,null,"#alter-msg",data);
                                    }
                                }
                            } catch (error) {
                                that.file=null;
                                echoMsg("#alter-msg",{status:4,message:"文件传输有误，将不会添加用户！"});
                                getData("{!! config('var.ca') !!}"+that.contest.cid,null,"#alter-msg",data);
                                return;
                            }
                        }
                    }else{
                        getData("{!! config('var.ca') !!}"+this.contest.cid,null,"#alter-msg",data);
                    }
                    
                },
                init(){
                    CKSource.Editor.create( document.querySelector('#altereditor' ),editorconfig)
                    .then( newEditor => {altereditor=newEditor;})
                    .catch( error => {console.error( error );});
                    filterTypes(this.ctypes,this.typekey['all']);
                    let that = this;
                    document.getElementById('alter').addEventListener('show.bs.modal',function(event){
                        const cid = event.relatedTarget.getAttribute('data-bs-cid');
                        getData("{!! isset($utype)&&$utype=='a'?config('var.acg'):config('var.cg') !!}"+cid,
                        function(json){
                            if(json.data!==null){
                                that.contest = json.data.contest;
                                that.contest.cstart = that.contest.cstart.replace(' ','T');
                                that.contest.cend = that.contest.cend.replace(' ','T');
                                if(!('ips' in that.contest.coption)){
                                    that.contest.coption.ips=[];
                                }
                                if(!('pwd' in that.contest.coption)){
                                    that.contest.coption.pwd="";
                                }
                                if(!('rule' in that.contest.coption)){
                                    that.contest.coption.pwd="";
                                }
                                if(!('punish' in that.contest.coption)){
                                    that.contest.coption.punish="10";
                                }
                                if(!('rtrank' in that.contest.coption)){
                                    that.contest.coption.rtrank=true;
                                }
                                if(!('numlimit' in that.contest.coption)){
                                    that.contest.coption.numlimit="";
                                }
                                altereditor.setData(that.contest.cinfo);
                                console.log(that.contest);
                            }
                        },"#alter-msg",null,jump=false);
                    });

                },
            }
        }).mount("#alter");

    </script>