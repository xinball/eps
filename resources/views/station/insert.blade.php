<x-modal id="checkinsert" class="modal-sm" title="添加确认">
    <div class="text-center p-4 pb-4">
        您确定要添加该站点吗？
    </div>
    <x-slot name="footer">
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#insert" onclick="insertapp.insert()">创建</button>
        <button type="button" class="btn btn-outline-info"  data-bs-toggle="modal" data-bs-target="#insert">返回</button>
    </x-slot>
</x-modal>
    <x-modal id="insert" class="modal-fullscreen ckeditor" title="添加站点@@{{ sname===''?'':'-'+sname }}">    
        <div class="text-center p-4 pb-4">
            <h4 class="mb-3">基本信息</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="sname" class="form-label">名称</label>
                    <input type="text" class="form-control" id="sname" v-model="sname" placeholder="站点名称" required>
                </div>
                <div class="col-md-6">
                    <label for="sstate" class="form-label">站点状态</label>
                    <select class="form-select" v-model="sstate" id="sstate" required>
                        <option v-for="(sstate,index) in sstates" :key="index" :label="sstate" :value="index">@{{ sstate }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="addr" class="form-label">地址</label>
                    <input type="text" class="form-control" id="addr" v-model="addr" placeholder="站点地址描述" required>
                </div>
                <div class="col-md-6">
                    <label for="time" class="form-label">开放时间</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="time" v-model="time" placeholder="开放时间描述【不填写则根据时间配置自动生成】" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline">
                        <label for="p" class="form-check-label">支持核酸检测</label>
                        <input type="checkbox" class="form-check-input" id="p" v-model="p" required>
                    </div>
                    <div v-show="p===true" class="input-group">
                        <input type="number" class="form-control" id="pnum" min="0" v-model="pnum" placeholder="核酸检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline">
                        <label for="r" class="form-check-label">支持抗原检测</label>
                        <input type="checkbox" class="form-check-input" id="r" v-model="r" required>
                    </div>
                    <div v-show="r===true" class="input-group">
                        <input type="number" class="form-control" id="rnum" min="0" v-model="rnum" placeholder="抗原检测人数/日【不填写表示无限制】" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch form-check-inline">
                        <label for="v" class="form-check-label">支持疫苗接种</label>
                        <input type="checkbox" class="form-check-input" id="v" v-model="v" required>
                    </div>
                    <div v-show="v===true" class="input-group">
                        <input type="number" class="form-control" id="vnum" min="0" v-model="vnum" placeholder="疫苗接种人数/日【不填写表示无限制】" required>
                    </div>
                </div>
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">时间配置</h4>
            <div class="row g-3">
            </div>
            <hr class="my-4"><br><br>
            <h4 class="mb-3">站点描述</h4>
            <textarea id="deseditor" class="ckeditor"></textarea>
        </div>
        <x-slot name="footer">
            <div v-show="file!=null" class="progress" style="width: 200px">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" :style="{width:fileprogress + '%'}" ></div>
            </div>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkinsert">创建</button>
        </x-slot>
    </x-modal>
        <script>

            let editor={des:null};
            const insertapp=Vue.createApp({
                data() {
                    return{
                        sname: "",
                        sstate: "c",
                        city_id:0,
                        region_id:0,
                        slat:0,
                        slng:0,

                        p:0,
                        r:0,
                        v:0,
                        pnum:null,
                        rnum:null,
                        vnum:null,
                        addr:"",
                        time:"",
                        des: "",

                        stime:[
                            [{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}],
                            [{start:"",end:""}],[{start:"",end:""}],[{start:"",end:""}]
                        ],

                        sstates:{
                            o:"开放",
                            c:"关闭"
                        }
                    }
                },
                mounted(){
                    this.init();
                },
                methods:{
                    insert(){
                        let data={
                            sname:this.sname,
                            sstate:this.sstate,
                            city_id:this.city_id,
                            region_id:this.region_id,
                            slat:this.slat,
                            slng:this.slng,
                            p:this.p,
                            r:this.r,
                            v:this.v,
                            pnum:this.pnum,
                            rnum:this.rnum,
                            vnum:this.vnum,
                            addr:this.addr,
                            time:this.time,
                            des:editor.des.getData(),
                            stime:JSON.stringify(this.stime),
                            _token:"{{csrf_token()}}"
                        };
                        console.log(data);
                        getData("{!! config('var.asi') !!}",null,"#insert-msg",data);
                    },
                    init(){
                        CKSource.Editor.create( document.querySelector( '#deseditor' ),editorconfig)
                        .then( newEditor => {editor.des=newEditor;} )
                        .catch( error => {console.error( error );} );
                    },
                }
            }).mount("#insert");

        </script>