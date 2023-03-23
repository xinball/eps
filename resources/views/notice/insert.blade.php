<!--点击发布后的确认页面-->
<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该公告吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>
</x-modal>

    <!--跟修改一样的界面，唯一不同是修改要先初始化出以前的数据-->
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加公告@@{{ ntitle===''?'':'-'+ntitle }}">
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="ntitle" class="form-label">标题</label>
                    <input type="text" class="form-control" id="ntitle" v-model="ntitle" placeholder="公告标题" required>
                </div>
                <div class="col-md-6">
                    <label for="ndes" class="form-label">描述</label>
                    <input type="text" class="form-control" id="ndes" v-model="ndes" placeholder="公告描述" required>
                </div>
                <div class="col-md-6">
                    <label for="ntype" class="form-label">类型</label>
                    <select class="form-select" v-model="ntype" id="ntype" required>
                        <option v-for="(ntype,index) in ntypes" :key="index" :label="ntype.label" :value="index">@{{ ntype.label }}</option>
                    </select>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">选项配置</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="sendtype" class="form-label">发布选项</label>
                    <select class="form-select" v-model="sendtype" id="sendtype" required>
                        <option v-for="(sendtype,index) in sendtypes" :key="index" :label="sendtype" :value="index">@{{ sendtype }}</option>
                    </select>
                </div>
                <div v-show="sendtype==='o'" class="col-md-6">
                    <label for="ntime" class="form-label">发布时间</label>
                    <input type="datetime-local" class="form-control" id="ntime" v-model="ntime" placeholder="发布时间" required>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">详细描述</h4>
            <textarea id="inserteditor" class="ckeditor"></textarea>
        </div>

        <!--footer的作用是就算其他滑动了，footer的部分位置始终不变-->
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkinsert">@{{ sendtypes[sendtype] }}</button>
        </x-slot>
    </x-modal>

        <script>



            let inserteditor;
            const insertapp=Vue.createApp({
                data() {
                    return{
                        ntitle: "",
                        ndes: "",
                        ntype: "h",
                        //默认发布时间为现在
                        ntime: "{!! date("Y-m-d\TH:i:s") !!}",

                        sendtype: "n",
                        sendtypes:{
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

                    //插入
                    insert(){
                        let data={
                            ntitle:this.ntitle,
                            ndes:this.ndes,
                            ntype:this.ntype,
                            sendtype:this.sendtype,
                            ninfo:inserteditor.getData(),
                            _token:"{{csrf_token()}}"
                        };
                        //定时发布
                        if(this.sendtype==='o'){
                            data.ntime=this.ntime;
                        }
                        console.log(data);
                        getData("{!! config('var.ani') !!}",null,"#insert-msg",data);
                        
                    },

                    //初始化固定，但修改的初始化要载入原来数据
                    init(){
                        CKSource.Editor.create( document.querySelector( '#inserteditor' ),editorconfig)
                        .then( newEditor => {inserteditor=newEditor;} )
                        .catch( error => {console.error( error );} );
                        filterTypes(this.ntypes,this.typekey);
                    },
                }
            }).mount("#insert");

        </script>