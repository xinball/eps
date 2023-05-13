    <!--管理员端修改公告信息-->
    <x-modal id="alter" class="modal-fullscreen ckeditor" title="编辑公告-@@{{ notice.ntitle }}">
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="alterntitle" class="form-label">标题</label>
                    <input type="text" class="form-control" id="alterntitle" v-model="notice.ntitle" placeholder="公告标题" required>
                </div>
                <div class="col-md-6">
                    <label for="alterndes" class="form-label">描述</label>
                    <input type="text" class="form-control" id="alterndes" v-model="notice.ndes" placeholder="公告描述" required>
                </div>
                <div class="col-md-6">
                    <label for="alterntype" class="form-label">类型</label>
                    <select class="form-select" v-model="notice.ntype" id="alterntype" required>
                        <option v-for="(ntype,index) in ntypes" :key="index" :label="ntype.label" :value="index">@{{ ntype.label }}</option>
                    </select>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">选项配置</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="altersendtype" class="form-label">发布选项</label>
                    <select class="form-select" v-model="sendtype" id="altersendtype" required>
                        <option v-for="(sendtype,index) in sendtypes" :key="index" :label="sendtype" :value="index">@{{ sendtype }}</option>
                    </select>
                </div>
                <div v-show="sendtype==='o'" class="col-md-6">
                    <label for="alterntime" class="form-label">发布时间</label>
                    <input type="datetime-local" class="form-control" id="alterntime" v-model="notice.ntime" placeholder="发布时间" required>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">详细描述</h4>
            <textarea id="altereditor" class="ckeditor"></textarea>
        </div>

        <!--右下角不动的按钮-->
        <x-slot name="footer">
            <!--调用下方alter（）函数-->
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-outline-success"  @click="alter">@{{ sendtypes[sendtype] }}</button>
        </x-slot>
    </x-modal>

    <script>
        let altereditor;
        const alterapp=Vue.createApp({
            data() {
                return{
                    index:0,
                    nid:0,
                    notice:{
                        nid:"",
                        ntitle:"",
                        ndes:"",
                        ntype:"",
                        ninfo:"",
                        ntime:""
                    },
                    sendtype: "n",
                    sendtypes:{
                        l:"发布（不更改发布时间）",
                        n:"即时发布",
                        o:"定时发布",
                    },
                    ntypes:{!! json_encode($config_notice['type']) !!},
                    typekey:{!! json_encode($config_notice['typekey']['all']) !!},
                }
            },
            mounted(){
                this.init();
            },
            methods:{
                //点击更改按钮时修改
                alter(){
                    let data={
                        ntitle:this.notice.ntitle,
                        ndes:this.notice.ndes,
                        ntype:this.notice.ntype,
                        sendtype:this.sendtype,
                        ninfo:altereditor.getData(),
                        _token:"{{csrf_token()}}"
                    };
                    if(this.sendtype==='o'){
                        data.ntime=this.notice.ntime;
                    }
                    console.log(data);
                    //前后端接口，调用alter
                    getData("{!! config('var.ana') !!}"+this.notice.nid,function(){
                        if(json.status===1){
                            if(typeof noticelist !== 'undefined'){
                                noticelist.getData();
                            }
                            $('#alter').modal("hide");
                        }
                    },"#alter-msg",data);                    
                },
                //初始化，点进去要先显示原本的数据，因此要先用aget取出来
                init(){
                    CKSource.Editor.create( document.querySelector( '#altereditor' ),editorconfig)
                    .then( newEditor => {altereditor=newEditor;} )
                    .catch( error => {console.error( error );} );
                    filterTypes(this.ntypes,this.typekey);
                    let that = this;
                    document.getElementById('alter').addEventListener('show.bs.modal',function(event){
                        getData("{!! config('var.ang') !!}"+that.nid,function(json){
                            if(json.data!==null){
                                that.notice = json.data.notice;
                                that.notice.ntime = that.notice.ntime.replace(' ','T');
                                document.title+="-"+that.notice.ntitle;
                                altereditor.setData(that.notice.ninfo);
                            }
                        },"#msg",null);
                    });
                },
            }
        }).mount("#alter");


    </script>