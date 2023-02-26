<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该比赛吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>
</x-modal>
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加比赛@@{{ ctitle===''?'':'-'+ctitle }}">
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ctitle" class="form-label">标题</label>
                    <input type="text" class="form-control" id="ctitle" v-model="ctitle" placeholder="比赛标题" required>
                </div>
                <div class="col-md-6">
                    <label for="cdes" class="form-label">描述</label>
                    <input type="text" class="form-control" id="cdes" v-model="cdes" placeholder="比赛描述" required>
                </div>
                <div class="col-md-6">
                    <label for="cstart" class="form-label">开始时间</label>
                    <input type="datetime-local" class="form-control" id="cstart" v-model="cstart" placeholder="开始时间" required>
                </div>
                <div class="col-md-6">
                    <label for="cend" class="form-label">结束时间</label>
                    <input type="datetime-local" class="form-control" id="cend" v-model="cend" placeholder="结束时间" required>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">选项配置</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ctype" class="form-label">类型</label>
                    <select class="form-select" v-model="ctype" id="ctype" required>
                        <option v-for="(ctype,index) in ctypes"  :key="index" :label="ctype" :value="index">@{{ ctype }}</option>
                        <option label="未选择类型" value="0" disabled="disabled"></option>
                    </select>
                </div>
                <div v-if="typekey.o.includes(ctype)" class="col-md-6">
                    <label for="pwd" class="form-label">密码</label>
                    <input type="text" class="form-control" id="pwd" v-model="pwd" placeholder="密码【不填写则不设置】" required>
                </div>
                <div v-if="typekey.s.includes(ctype)" class="col-md-6">
                    <label for="usersource" class="form-label">参与比赛用户来源</label>
                    <select class="form-select" v-model="usersource" id="usersource" required>
                        <option v-for="(usersource,index) in usersources" :key="index" :label="usersource" :value="index">@{{ usersource }}</option>
                    </select>
                </div>
                <div v-if="typekey.s.includes(ctype)&&usersource==='f'" class="col-md-6">
                    <label for="filepath" class="form-label">用户名/用户编号序列文件<i class="bi bi-filetype-csv"></i></label>
                    <input type="file" class="form-control" @change="getuserlist($event)" accept=".csv" id="filepath" required>
                </div>
                <div v-if="typekey.s.includes(ctype)&&usersource==='c'" class="col-md-6">
                    <label for="prefix" class="form-label">用户前缀</label>
                    <input type="text" class="form-control" v-model="prefix" id="prefix" placeholder="用户名前缀" required>
                </div>
                <div v-if="typekey.s.includes(ctype)&&usersource==='c'" class="col-md-6">
                    <label for="startnum" class="form-label">起始编号</label>
                    <input type="number" class="form-control" v-model="startnum" id="startnum" placeholder="起始编号" required>
                </div>
                <div v-if="typekey.s.includes(ctype)&&usersource==='c'" class="col-md-6">
                    <label for="endnum" class="form-label">结束编号</label>
                    <input type="number" class="form-control" v-model="endnum" id="endnum" placeholder="结束编号" required>
                </div>
                <div class="col-md-12">
                    <label for="ip" class="form-label">允许IP范围
                        <button type="button" class="btn btn-outline-info" @click="insertip"><i class="bi bi-plus-lg"></i></button>
                    </label>
                    <div class="input-group" v-for="(ip,index) in ips" :key="index">
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
                        <input type="radio" name="rule" class="form-check-input" id="rulea" v-model="rule" value="a" required>
                        <label for="rulea" class="form-check-label">ACM</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="rule" class="form-check-input" id="rulei" v-model="rule" value="i" required>
                        <label for="rulei" class="form-check-label">IOI</label>
                    </div>
                    <a href="http://oj.maythorn.top/notice/13" target="_blank" class="btn btn-outline-info">赛制介绍</a>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline">
                        <label for="rtrank" class="form-check-label">实时排名</label>
                        <input type="checkbox" class="form-check-input" id="rtrank" v-model="rtrank" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="numlimit" class="form-label">提交次数限制【不填写表示无限制】</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="numlimit" v-model="numlimit" placeholder="提交次数限制【不填写表示无限制】" required>
                    </div>
                </div>
                <div v-if="rule==='a'" class="col-md-6">
                    <label for="punish" class="form-label">罚时【不填写表示不罚时】</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="punish" v-model="punish" placeholder="罚时【不填写表示不罚时】" required>
                        <span class="input-group-text">分钟</span>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">详细描述</h4>
            <textarea id="editor" class="ckeditor"></textarea>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">题目<div class="input-group"><input class="form-control" type="number" v-model="pid" placeholder="请输入问题编号"> <button type="button" class="btn btn-outline-info" @click="insertpid"><i class="bi bi-plus-lg"></i> 添加题目</button></div></h4>
            <div class="row g-3">
                <div class="col-md-12 input-group" v-for="(pid,index) in pids" :key="index">
                    <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" :href="'/problem/'+pid">@{{ "#"+pid+" "+problems[index].ptitle+" "+problems[index].pdes }}</a>
                    <button type="button" class="btn btn-outline-danger" @click="delpid(index)"><i class="bi bi-x-lg"></i></button>
                    <button type="button" class="btn btn-outline-info" @click="uppid(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" class="btn btn-outline-secondary" @click="downpid(index)" :disabled="index===pids.length-1"><i class="bi bi-arrow-down"></i></button>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">比赛管理员<div class="input-group"><input class="form-control" type="number" v-model="uid" placeholder="请输入用户名/邮箱/ID"> <button type="button" class="btn btn-outline-info" @click="insertuid"><i class="bi bi-plus-lg"></i> 添加管理员</button></div></h4>
            <div class="row g-3">
                <div class="col-md-12 input-group" v-for="(uid,index) in auids" :key="index">
                    <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                    <a class="form-control text-truncate btn btn-outline-secondary" target="_blank" :href="'/user/'+uid">@{{ "#"+uid+" "+ausers[index].uname+" "+ausers[index].uemail }}</a>
                    <button type="button" class="btn btn-outline-danger" @click="deluid(index)"><i class="bi bi-x-lg"></i></button>
                    <button type="button" class="btn btn-outline-info" @click="upuid(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                    <button type="button" class="btn btn-outline-secondary" @click="downuid(index)" :disabled="index===auids.length-1"><i class="bi bi-arrow-down"></i></button>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div v-show="file!=null" class="progress" style="width: 200px">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{width:fileprogress + '%'}" ></div>
            </div>
            <button type="button" class="btn btn-outline-success"  data-bs-toggle="modal" data-bs-target="#checkinsert">创建</button>
        </x-slot>

    </x-modal>
        <script>
            let editor;
            const insertapp=Vue.createApp({
                data() {
                    return{
                        ctitle: "",
                        cdes: "",
                        ctype: "0",
                        cstart: "2022-01-01T00:00:00",
                        cend: "2023-01-01T00:00:00",

                        prefix: "",
                        startnum: "",
                        endnum: "",
                        usersource: "c",

                        ips: [],
                        rule: "a",
                        pwd: "",
                        punish:"10",
                        rtrank:true,
                        numlimit:"",

                        auids:[],
                        ausers: [],
                        pids: [],
                        problems: [],
                        pid:"",
                        file: null,
                        fileprogress: 0,
                        ctypes:isJSON({!! json_encode($config_contest['type'],JSON_UNESCAPED_UNICODE) !!}),
                        userscources:isJSON({!! json_encode($config_contest['usersource'],JSON_UNESCAPED_UNICODE) !!}),
                        typekey:{!! json_encode($config_contest['typekey']) !!}
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{
                insertuid(){
                    if(!this.auids.includes(this.uid)){
                        let that=this;
                        getData("{!! isset($utype)&&$utype=='a'?config('var.aug'):config('var.ug') !!}"+this.uid,
                        function(json){
                            if(json.data!==null){
                                if(!'user' in json.data){
                                    echoMsg("#insert-msg",{status:4,message:"查询不到该编号对应的用户数据！"});
                                }else{
                                    that.auids.push(that.uid);
                                    that.ausers.push(json.data.user);
                                }
                            }
                        },"#insert-msg",null,false);
                    }else{
                        echoMsg("#insert-msg",{status:4,message:"该用户已添加"});
                    }
                },
                deluid(index){
                    this.auids.splice(index,1);
                    this.ausers.splice(index,1);
                },

                upuid(index){
                    if(index>0){
                        let tem=this.auids[index];
                        this.auids[index]=this.auids[index-1];
                        this.auids[index-1]=tem;
                        let temproblem=this.ausers[index];
                        this.ausers[index]=this.ausers[index-1];
                        this.ausers[index-1]=temproblem;
                    }
                },
                downuid(index){
                    if(index<this.auids.length-1){
                        let tem=this.auids[index];
                        this.auids[index]=this.auids[index+1];
                        this.auids[index+1]=tem;
                        let temproblem=this.ausers[index];
                        this.ausers[index]=this.ausers[index+1];
                        this.ausers[index+1]=temproblem;
                    }
                },
                    getuserlist(event){
                        this.file=event.currentTarget.files[0];
                        this.fileprogress=0;
                    },
                    insertip(){
                        this.ips.push({start:"",end:""});
                    },
                    delip(index){
                        this.ips.splice(index,1);
                    },
                    insertpid(){
                        if(!this.pids.includes(this.pid)){
                            let that=this;
                            getData("{!! isset($utype)&&$utype=='a'?config('var.apg'):config('var.pg') !!}"+this.pid,
                            function(json){
                                if(json.data!==null){
                                    if(!'problem' in json.data||!'ptype' in json.data.problem){
                                        echoMsg("#insert-msg",{status:4,message:"查询不到该编号对应的问题数据！"});
                                    }else if(json.data.problem.ptype!=='m'){
                                        echoMsg("#insert-msg",{status:4,message:"该问题类型不为可加入比赛类型，无法添加！"});
                                    }else{
                                        that.pids.push(that.pid);
                                        that.problems.push(json.data.problem);
                                    }
                                }
                            },"#insert-msg",null,false);
                        }else{
                            echoMsg("#insert-msg",{status:4,message:"该标签已添加"});
                        }
                    },
                    delpid(index){
                        this.pids.splice(index,1);
                        this.problems.splice(index,1);
                    },
                    uppid(index){
                        if(index>0){
                            let tem=this.pids[index];
                            let temproblem=this.problems[index];
                            this.pids[index]=this.pids[index-1];
                            this.pids[index-1]=tem;
                            this.problems[index]=this.problems[index-1];
                            this.problems[index-1]=temproblem;
                        }
                    },
                    downpid(index){
                        if(index<this.pids.length-1){
                            let tem=this.pids[index];
                            let temproblem=this.problems[index];
                            this.pids[index]=this.pids[index+1];
                            this.pids[index+1]=tem;
                            this.problems[index]=this.problems[index+1];
                            this.problems[index+1]=temproblem;
                        }
                    },
                    insert(){
                        let data={
                            ctitle:this.ctitle,
                            cdes:this.cdes,
                            ctype:this.ctype,
                            cstart:this.cstart,
                            cend:this.cend,
                            cinfo:editor.getData(),
                            pids:JSON.stringify(this.pids),
                            ips:JSON.stringify(this.ips),
                            rule:this.rule,
                            punish:this.punish,
                            rtrank:this.rtrank,
                            numlimit:this.numlimit,
                            pwd:this.pwd,
                            utype:"{{ $utype }}",
                            _token:"{{csrf_token()}}"
                        };
                        if(this.typekey.s.includes(this.ctype)){
                            data.pwd="";
                            data.usersource=this.usersource;
                            if(this.usersource==='c'){
                                data.prefix=this.prefix;
                                data.startnum=this.startnum;
                                data.endnum=this.endnum;
                                getData("{!! config('var.ci') !!}",null,"#insert-msg",data);
                            }else{
                                let that=this;
                                that.fileprogress=0;
                                try {
                                    if(this.file==null){
                                        getData("{!! config('var.ci') !!}",null,"#insert-msg",data);
                                        // echoMsg("#insert-msg",{status:4,message:"请选择要上传的文件！"});
                                        return;
                                    }
                                    const total=this.file.size;
                                    if(total>10240){
                                        echoMsg("#insert-msg",{status:4,message:"文件大小不得大于10KB！"});
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
                                            getData("{!! config('var.ci') !!}",null,"#insert-msg",data);
                                        }
                                    }
                                } catch (error) {
                                    that.file=null;
                                    echoMsg("#insert-msg",{status:4,message:"文件传输有误，将不会添加用户！"});
                                    getData("{!! config('var.ci') !!}",null,"#insert-msg",data);
                                    return;
                                }
                            }
                        }else{
                            getData("{!! config('var.ci') !!}",null,"#insert-msg",data);
                        }
                        console.log(data);                        
                    },
                    init(){
                        CKSource.Editor.create( document.querySelector( '#editor' ),editorconfig)
                        .then( newEditor => {editor=newEditor;})
                        .catch( error => {console.error( error );});
                        filterTypes(this.ctypes,this.typekey[{!! json_encode($utype) !!}]);
                    },
                }
            }).mount("#insert");

            // $('#insert').modal({
            //     focus:false,
            //     dissmissible:false
            // });

            // const watchdog = new CKSource.EditorWatchdog();
            // window.watchdog = watchdog;
            // watchdog.setCreator( ( element, config ) => {
            //     return CKSource.Editor
            //         .create( element, config )
            //         .then( editor => {
            //             return editor;
            //         } )
            // } );
            // watchdog.setDestructor( editor => {
            //     return editor.destroy();
            // } );
            // watchdog.on( 'error', handleError );
            // watchdog
            //     .create( document.querySelector( '#editor' ), {
            //         licenseKey: '',
            //     } )
            //     .catch( handleError );
            // function handleError( error ) {
            //     console.error( 'Oops, something went wrong!' );
            //     console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
            //     console.warn( 'Build id: w16v5o3j2oo5-6xtzsplb0yzp' );
            //     console.error( error );
            // }


            // Vue.createApp({
            //     name: 'app',
            //     components: {
            //         ckeditor: CKSource.Editor.component
            //     },
            //     data() {
            //         return {
            //             editor: CKSource.Editor,
            //             content: '哈哈</hr>'
            //         }
            //     }
            // });
            // export default {
            //     name: 'app',
            //     components: {
            //         ckeditor: CKSource.component
            //     },
            //     data() {
            //         return {
            //             editor: CKSource.Editor,
            //             content: '哈哈</hr>'
            //         }
            //     }
            // }
        </script>