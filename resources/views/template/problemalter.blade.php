
    <x-modal id="alter" class="modal-fullscreen ckeditor" title="编辑问题-@@{{ problem.ptitle }}">
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="alterptitle" class="form-label">标题</label>
                    <input type="text" class="form-control" id="alterptitle" v-model="problem.ptitle" placeholder="题目标题" required>
                </div>
                <div class="col-md-6">
                    <label for="alterpdes" class="form-label">描述</label>
                    <input type="text" class="form-control" id="alterpdes" v-model="problem.pdes" placeholder="题目描述" required>
                </div>
                <div class="col-md-12">
                    <label for="alterineditor" class="form-label">输入描述</label>
                    <textarea id="alterineditor" class="ckeditor"></textarea>
                </div>
                <div class="col-md-12">
                    <label for="alterouteditor" class="form-label">输出描述</label>
                    <textarea id="alterouteditor" class="ckeditor"></textarea>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">选项配置</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="alterptype" class="form-label">类型</label>
                    <select class="form-select" v-model="problem.ptype" id="alterptype" required>
                        <option v-for="(ptype,index) in ptypes" :key="index" :label="ptype" :value="index">@{{ ptype }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="altertimelimit" class="form-label">时间限制</label>
                    <div class="input-group">
                    <input type="number" min="1" max="3000" class="form-control" id="altertimelimit" v-model="problem.poption.timelimit" placeholder="时间限制(ms)" required>
                    <span class="input-group-text">ms</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="alterspacelimit" class="form-label">空间限制</label>
                    <div class="input-group">
                    <input type="number" min="1" max="100000" class="form-control" id="alterspacelimit" v-model="problem.poption.spacelimit" placeholder="空间限制(KB)" required>
                    <span class="input-group-text">KB</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="altertip" class="form-label">提示</label>
                    <input type="text" class="form-control" id="altertip" v-model="problem.poption.tip" placeholder="提示" required>
                </div>
                <div class="col-md-6">
                    <label for="altersource" class="form-label">来源</label>
                    <input type="text" class="form-control" id="altertsource" v-model="problem.poption.source" placeholder="题目来源" required>
                </div>
                <div class="col-md-12">
                    <label for="altertids" class="form-label">标签</label>
                    <div class="input-group">
                        <button v-for="(tid,index) in problem.tids" type="button" class="btn btn-outline-dark" :key="index" @click="deltid(index)">@{{ tags[tid].tname }} <i class="bi bi-x-lg"></i></button>
                        <select class="form-select" v-model="tid" id="altertids">
                            <option v-for="tag in tags0" :key="tag.tid" :label="tag.tname" :value="tag.tid">@{{ tag.tname }}</option>
                            <option label="未选择标签" value="0" disabled="disabled"></option>
                        </select>
                        <button type="button" class="btn btn-outline-success" @click="inserttid">添加</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="alterrule" class="form-label">规则</label>
                    <input type="text" class="form-control" id="alterrule" v-model="rule" placeholder="规则" required>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">详细描述</h4>
            <textarea id="alterdeseditor" class="ckeditor"></textarea>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">示例配置【用户测试使用】<button type="button" class="btn btn-outline-info" @click="insertcase"><i class="bi bi-plus-lg"></i> 添加示例</button></h4>
            <div class="row g-3">
                <div class="col-md-12" v-for="(caseitem,index) in problem.poption.cases" :key="index">
                    <div class="input-group">
                        <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                        <textarea type="text" rows="3" style="resize:none;" class="form-control" v-model="caseitem.in" placeholder="输入" required></textarea>
                        <textarea type="text" rows="3" style="resize:none;" class="form-control"  v-model="caseitem.out" placeholder="输出" required></textarea>
                        <button type="button" class="btn btn-outline-danger" @click="delcase(index)"><i class="bi bi-x-lg"></i></button>
                        <button type="button" class="btn btn-outline-info" @click="upcase(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary" @click="downcase(index)" :disabled="index===problem.poption.cases.length-1"><i class="bi bi-arrow-down"></i></button>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">测评用例配置【评测使用】<a target="_blank" href="http://oj.maythorn.top/notice/12" class="btn btn-outline-info">用例格式详解</a></i>
                <input type="file" class="form-control" @change="getcases($event)" accept=".zip" id="alterfilepath" required></h4>
                <label class="input-group-text" for="alterfilepath">上传zip压缩包文件 <i class="bi bi-file-zip-fill"></i></label>
            <div class="row g-3">
                <div class="col-md-12" v-for="(pcase,index) in problem.pcases" :key="index">
                    <div class="input-group">
                        <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                        <span class="form-control" v-text="pcase.in"></span>
                        <span class="form-control" v-text="pcase.out"></span>
                        <input class="form-control" type="text" v-model="pcase.score" placeholder="分数">
                    </div>
                </div>
            </div>
        </div>
        <x-slot name="footer">
            <div v-show="file!=null" class="progress" style="width: 200px">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{width:fileprogress + '%'}" ></div>
            </div>
            <button type="button" class="btn btn-outline-success"  @click="alter">修改</button>
        </x-slot>
    </x-modal>
        <script>

            let altereditor={des:null,in:null,out:null};
            const alterapp=Vue.createApp({
                data() {
                    return{
                        problem:{
                            pid:"",
                            ptitle:"",
                            pdes:"",
                            ptype: "",
                            pinfo:"",
                            poption:{
                                cases:[],
                                in:"",
                                out:"",
                                timelimit:"",
                                spacelimit:"",
                                source:"",
                            },
                            pcases:[],
                            tids:"",
                        },

                        rule: "",
                        tid:"0",
                        // tip: "",
                        // timelimit: "100",
                        // spacelimit: "1000",
                        // cases: [
                        //     {in:"",out:""}
                        // ],

                        tags0:[],
                        tags:[],
                        ptypes:[],
                        file: null,
                        fileprogress: 0,
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{
                    getcases(event){
                        this.file=event.currentTarget.files[0];
                        this.problem.pcases=[];
                        let that=this;
                        JSZip.loadAsync(this.file)
                        .then(function(zip) {
                            // var dateAfter = new Date();
                            // $title.append($("<span>", {
                            //     "class": "small",
                            //     text:" (loaded in " + (dateAfter - dateBefore) + "ms)"
                            // }));
                            let pcases=[];
                            zip.forEach(function (relativePath, zipEntry) {
                                pcases.push(zipEntry.name);
                            });
                            for(i=1;i<=pcases.length/2;++i){
                            console.log(i);
                                if(pcases.includes(i+".in")&&pcases.includes(i+".out")){
                                    that.problem.pcases.push({in:(i+".in"),out:(i+".out"),score:""});
                                }else{
                                    if(i===1){
                                        echoMsg("#alter-msg",{status:4,message:"样例文件序列有误！"});
                                    }
                                    break;
                                }
                            }
                            console.log(that.problem.pcases);
                        }, function (e) {
                            echoMsg("#alter-msg",{status:4,message:e.message});
                        });
                    },
                    inserttid(){
                        if(!this.problem.tids.includes(this.tid)){
                            if(this.problem.tids.length>=6){
                                echoMsg("#alter-msg",{status:4,message:"标签数量不得超过6个"});
                            }else if(this.tid in this.tags)
                                this.problem.tids.push(this.tid);
                        }else{
                            echoMsg("#alter-msg",{status:4,message:"该标签已添加"});
                        }
                    },
                    deltid(index){
                        this.problem.tids.splice(index,1);
                    },
                    insertcase(){
                        this.problem.poption.cases.push({in:"",out:""});
                    },
                    delcase(index){
                        this.problem.poption.cases.splice(index,1);
                    },
                    upcase(index){
                        if(index>0){
                            let tem=this.problem.poption.cases[index];
                            this.problem.poption.cases[index]=this.problem.poption.cases[index-1];
                            this.problem.poption.cases[index-1]=tem;
                        }
                    },
                    downcase(index){
                        if(index<this.cases.length-1){
                            let tem=this.problem.poption.cases[index];
                            this.problem.poption.cases[index]=this.problem.poption.cases[index+1];
                            this.problem.poption.cases[index+1]=tem;
                        }
                    },
                    alter(){
                        let data={
                            ptitle:this.problem.ptitle,
                            pdes:this.problem.pdes,
                            ptype:this.problem.ptype,
                            pinfo:altereditor.des.getData(),
                            pcases:JSON.stringify(this.problem.pcases),

                            in:altereditor.in.getData(),
                            out:altereditor.out.getData(),
                            tip:this.problem.poption.tip,
                            source:this.problem.poption.source,
                            tids:JSON.stringify(this.problem.tids),
                            timelimit: this.problem.poption.timelimit,
                            spacelimit:this.problem.poption.spacelimit,
                            cases:JSON.stringify(this.problem.poption.cases),
                            utype:"{{ $utype }}",
                            _token:"{{csrf_token()}}"
                        };
                        let fileFormData=new FormData();
                        for(i in data){
                            fileFormData.append(i,data[i]);
                        }
                        if(this.file!==null)
                            fileFormData.append('pcasesfile',this.file,this.file.name);
                        console.log(data);
                        $.ajax({
                            method:'POST',
                            url:"{!! config('var.pa') !!}"+this.problem.pid,
                            data:fileFormData,
                            contentType:false,
                            processData:false,
                            success:function(result){
                            let json = isJSON(result);
                                echoMsg("#alter-msg",json);
                            }
                        });
                        // getData("{!! config('var.pa') !!}"+this.problem.pid,null,"#alter-msg",data);
                        
                    },
                    init(){
                        CKSource.Editor.create( document.querySelector( '#alterdeseditor' ),editorconfig)
                        .then( newEditor => {altereditor.des=newEditor;} )
                        .catch( error => {console.error( error );} );
                        CKSource.Editor.create( document.querySelector( '#alterineditor' ),editorconfig)
                        .then( newEditor => {altereditor.in=newEditor;} )
                        .catch( error => {console.error( error );} );
                        CKSource.Editor.create( document.querySelector( '#alterouteditor' ),editorconfig)
                        .then( newEditor => {altereditor.out=newEditor;} )
                        .catch( error => {console.error( error );} );
                        this.ptypes=isJSON({!! json_encode($config_problem['type'],JSON_UNESCAPED_UNICODE) !!});
                        this.typekey={!! json_encode($config_problem['typekey'][$utype]) !!};
                        for(index in this.ptypes)
                            if(!this.typekey.includes(index))
                                delete this.ptypes[index];
                        let that = this;
                        document.getElementById('alter').addEventListener('show.bs.modal',function(event){
                            const pid = event.relatedTarget.getAttribute('data-bs-pid');
                            getData("{!! isset($utype)&&$utype=='a'?config('var.apg'):config('var.pg') !!}"+pid,
                            function(json){
                                if(json.data!==null){
                                    getTags(json,that.tags,that.tags0);
                                    let problem=json.data.problem;
                                    document.title+=problem.ptitle;
                                    problem.pcases=isJSON(problem.pcases,true);
                                    problem.poption=isJSON(problem.poption);
                                    if(!('cases' in problem.poption)){
                                        problem.poption.cases=[];
                                    }
                                    if(!('in' in problem.poption)){
                                        problem.poption.in="";
                                    }
                                    if(!('out' in problem.poption)){
                                        problem.poption.out="";
                                    }
                                    if(!('timelimit' in problem.poption)){
                                        problem.poption.timelimit="100";
                                    }
                                    if(!('spacelimit' in problem.poption)){
                                        problem.poption.spacelimit="1000";
                                    }
                                    if(!('source' in problem.poption)){
                                        problem.poption.source="";
                                    }
                                    if(!('tip' in problem.poption)){
                                        problem.poption.tip="";
                                    }
                                    that.problem=problem;
                                    altereditor.des.setData(that.problem.pinfo);
                                    altereditor.in.setData(that.problem.poption.in);
                                    altereditor.out.setData(that.problem.poption.out);
                                    console.log(that.problem);
                                }
                            },"#alter-msg",null,jump=false);
                        });
                    },
                }
            }).mount("#alter");

        </script>