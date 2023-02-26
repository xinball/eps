<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该问题吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>
</x-modal>
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加问题@@{{ ptitle===''?'':'-'+ptitle }}">    
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ptitle" class="form-label">标题</label>
                    <input type="text" class="form-control" id="ptitle" v-model="ptitle" placeholder="题目标题" required>
                </div>
                <div class="col-md-6">
                    <label for="pdes" class="form-label">描述</label>
                    <input type="text" class="form-control" id="pdes" v-model="pdes" placeholder="题目描述" required>
                </div>
                <div class="col-md-12">
                    <label for="ineditor" class="form-label">输入描述</label>
                    <textarea id="ineditor" class="ckeditor"></textarea>
                </div>
                <div class="col-md-12">
                    <label for="outeditor" class="form-label">输出描述</label>
                    <textarea id="outeditor" class="ckeditor"></textarea>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">选项配置</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ptype" class="form-label">类型</label>
                    <select class="form-select" v-model="ptype" id="ptype" required>
                        <option v-for="(ptype,index) in ptypes" :key="index" :label="ptype" :value="index">@{{ ptype }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="timelimit" class="form-label">时间限制</label>
                    <div class="input-group">
                        <input type="number" min="1" max="3000" class="form-control" id="timelimit" v-model="timelimit" placeholder="时间限制(ms)" required>
                        <span class="input-group-text">ms</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="spacelimit" class="form-label">空间限制</label>
                    <div class="input-group">
                    <input type="number" min="1" max="100000" class="form-control" id="spacelimit" v-model="spacelimit" placeholder="空间限制(KB)" required>
                    <span class="input-group-text">KB</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="tip" class="form-label">提示</label>
                    <input type="text" class="form-control" id="tip" v-model="tip" placeholder="提示" required>
                </div>
                <div class="col-md-6">
                    <label for="source" class="form-label">来源</label>
                    <input type="text" class="form-control" id="source" v-model="source" placeholder="题目来源" required>
                </div>
                <div class="col-md-12">
                    <label for="tids" class="form-label">标签</label>
                    <div class="input-group">
                        <button v-for="(tid,index) in tids" type="button" class="btn btn-outline-dark" :key="index" @click="deltid(index)">@{{ tags[tid].tname }} <i class="bi bi-x-lg"></i></button>
                        <select class="form-select" v-model="tid" id="tids">
                            <option v-for="tag in tags0" :key="tag.tid" :label="tag.tname" :value="tag.tid">@{{ tag.tname }}</option>
                            <option label="未选择标签" value="0" disabled="disabled"></option>
                        </select>
                        <button type="button" class="btn btn-outline-success" @click="inserttid">添加</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="rule" class="form-label">规则</label>
                    <input type="text" class="form-control" id="rule" v-model="rule" placeholder="规则" required>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">详细描述</h4>
            <textarea id="deseditor" class="ckeditor"></textarea>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">示例配置【用户测试使用】<button type="button" class="btn btn-outline-info" @click="insertcase"><i class="bi bi-plus-lg"></i> 添加示例</button></h4>
            <div class="row g-3">
                <div class="col-md-12" v-for="(caseitem,index) in cases" :key="index">
                    <div class="input-group">
                        <button type="button" class="badge bg-dark" disabled>@{{ index+1 }}</button>
                        <textarea type="text" rows="3" style="resize:none;" :id="'casein'+index" class="form-control" v-model="caseitem.in" placeholder="输入" required></textarea>
                        <textarea type="text" rows="3" style="resize:none;" :id="'caseout'+index" class="form-control"  v-model="caseitem.out" placeholder="输出" required></textarea>
                        <button type="button" class="btn btn-outline-danger" @click="delcase(index)"><i class="bi bi-x-lg"></i></button>
                        <button type="button" class="btn btn-outline-info" @click="upcase(index)" :disabled="index===0"><i class="bi bi-arrow-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary" @click="downcase(index)" :disabled="index===cases.length-1"><i class="bi bi-arrow-down"></i></button>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">测评用例配置【评测机使用】<a target="_blank" href="http://oj.maythorn.top/notice/12" class="btn btn-outline-info">用例格式详解</a></h4>
            <div class="input-group">
                <input type="file" class="form-control" @change="getcases($event)" accept=".zip" id="insertfilepath" required>
                <label class="input-group-text" for="insertfilepath">上传zip压缩包文件 <i class="bi bi-file-zip-fill"></i></label>
            </div>
            <div class="row g-3">
                <div class="col-md-12" v-for="(pcase,index) in pcases" :key="index">
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
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkinsert">创建</button>
        </x-slot>
    </x-modal>
        <script>

            let editor={des:null,in:null,out:null};
            const insertapp=Vue.createApp({
                data() {
                    return{
                        tags0:[],
                        tags:[],

                        ptitle: "",
                        pdes: "",
                        ptype: "h",
                        pcases: [],

                        rule: "",

                        tid:"0",
                        tids:[],
                        tip: "",
                        timelimit: "100",
                        spacelimit: "1000",
                        source:"",
                        cases: [
                            {in:"",out:""}
                        ],

                        file: null,
                        fileprogress: 0,
                        ptypes:[],
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{
                    getcases(event){
                        this.file=event.currentTarget.files[0];
                        this.pcases=[];
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
                                if(pcases.includes(i+".in")&&pcases.includes(i+".out")){
                                    that.pcases.push({in:(i+".in"),out:(i+".out"),score:""});
                                }else{
                                    if(i===1){
                                        echoMsg("#insert-msg",{status:4,message:"样例文件序列有误！"});
                                    }
                                    break;
                                }
                            }
                        }, function (e) {
                            echoMsg("#insert-msg",{status:4,message:e.message});
                        });
                    },
                    inserttid(){
                        if(!this.tids.includes(this.tid)){
                            if(this.tids.length>=6){
                                echoMsg("#insert-msg",{status:4,message:"标签数量不得超过6个"});
                            }else if(this.tid in this.tags)
                                this.tids.push(this.tid);
                        }else{
                            echoMsg("#insert-msg",{status:4,message:"该标签已添加"});
                        }
                    },
                    deltid(index){
                        this.tids.splice(index,1);
                    },
                    insertcase(){
                        this.cases.push({in:"",out:""});
                        //alert(JSON.stringify(this.cases));
                    },
                    delcase(index){
                        this.cases.splice(index,1);
                    },
                    upcase(index){
                        if(index>0){
                            let tem=this.cases[index];
                            this.cases[index]=this.cases[index-1];
                            this.cases[index-1]=tem;
                        }
                    },
                    downcase(index){
                        if(index<this.cases.length-1){
                            let tem=this.cases[index];
                            this.cases[index]=this.cases[index+1];
                            this.cases[index+1]=tem;
                        }
                    },
                    insert(){
                        let data={
                            ptitle:this.ptitle,
                            pdes:this.pdes,
                            ptype:this.ptype,
                            pinfo:editor.des.getData(),
                            pcases:JSON.stringify(this.pcases),

                            in:editor.in.getData(),
                            out:editor.out.getData(),
                            tip:this.tip,
                            source:this.source,
                            tids:JSON.stringify(this.tids),
                            timelimit: this.timelimit,
                            spacelimit:this.spacelimit,
                            cases:JSON.stringify(this.cases),
                            utype:"{{ $utype }}",
                            _token:"{{csrf_token()}}"
                        };
                        let fileFormData=new FormData();
                        for(i in data){
                            fileFormData.append(i,data[i]);
                        }
                        if(this.file!==null){
                            fileFormData.append('pcasesfile',this.file,this.file.name);
                        }
                        console.log(data);
                        $.ajax({
                            method:'POST',
                            url:"{!! config('var.pi') !!}",
                            data:fileFormData,
                            contentType:false,
                            processData:false,
                            success:function(result){
                            let json = isJSON(result);
                                echoMsg("#insert-msg",json);
                            }
                        });
                        console.log(data);
                        // getData("{!! config('var.pi') !!}",null,"#insert-msg",data);
                        
                    },
                    init(){
                        CKSource.Editor.create( document.querySelector( '#deseditor' ),editorconfig)
                        .then( newEditor => {editor.des=newEditor;} )
                        .catch( error => {console.error( error );} );
                        CKSource.Editor.create( document.querySelector( '#ineditor' ),editorconfig)
                        .then( newEditor => {editor.in=newEditor;} )
                        .catch( error => {console.error( error );} );
                        CKSource.Editor.create( document.querySelector( '#outeditor' ),editorconfig)
                        .then( newEditor => {editor.out=newEditor;} )
                        .catch( error => {console.error( error );} );
                        this.ptypes=isJSON({!! json_encode($config_problem['type'],JSON_UNESCAPED_UNICODE) !!});
                        this.typekey={!! json_encode($config_problem['typekey'][$utype]) !!};
                        for(index in this.ptypes)
                            if(!this.typekey.includes(index))
                                delete this.ptypes[index];
                        let that=this;
                        getData("{{ config('var.tl') }}",
                        function(json){
                            if(json.tags!==null){
                                getTags(json,that.tags,that.tags0);
                            }
                        });
                    },
                }
            }).mount("#insert");

        </script>