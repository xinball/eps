
<main id="operationlist" class="container list shadow">
<!--侧边栏-->
<x-offcanvas>
        <form>
            <div class="mb-3 col-12">
                <div class="input-group">
                    <select class="form-select" v-model="params.order">
                        <option v-for="(ordertype,index) in ordertypes" :key="index" :label="ordertype" :value="index">@{{ ordertype }}</option>
                    </select>
                    <button type="button" class="btn btn-outline-dark" @click="set('desc')" :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'"><i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></button>
                    <button type="button" class="btn btn-outline-info" @click="reset">重置 <i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="offcanvas"   @click="getData(params)">查询 <i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.oid" id="paramsoid" placeholder="操作编号">
                <label for="paramsoid">操作编号</label>
            </div>
@if (isset($utype)&&$utype==='a')
            <div class="mb-3 col-12 form-floating">
                <input type="number" class="form-control" v-model="params.uid" id="paramsuid" placeholder="用户编号">
                <label for="paramsuid">用户编号</label>
            </div>
@endif
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.oip" id="paramsoip" placeholder="IP">
                <label for="paramsoip">IP</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.ostart" id="paramsostart" placeholder="开始时间范围">
                <label for="paramsostart">开始时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="datetime-local" class="form-control" v-model="params.oend" id="paramsoend" placeholder="结束时间范围">
                <label for="paramsoend">结束时间范围</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.type" id="paramstype">
                    <option v-for="(otype,index) in otypes" :key="index" :label="otype" :value="index">@{{ otype }}</option>
                    <option value="" label="所有操作类型">所有操作类型</option>
                </select>
                <label for="paramstype">操作类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.status" id="paramsstatus">
                    <option v-for="(item,index) in statuses" :key="index" :value="index" :label="item.label">@{{ item.label }}</option>
                    <option value="" label="所有返回状态类型">所有返回状态类型</option>
                </select>
                <label for="paramsstatus">返回状态类型</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.device" id="paramsdevice" placeholder="设备">
                <label for="paramsdevice">设备</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.platform" id="paramsplatform" placeholder="平台">
                <label for="paramsplatform">平台</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.platformv" id="paramsplatformv" placeholder="平台版本">
                <label for="paramsplatformv">平台版本</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.browser" id="paramsbrowser" placeholder="浏览器">
                <label for="paramsbrowser">浏览器</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <input type="text" class="form-control" v-model="params.browserv" id="paramsbrowserv" placeholder="浏览器版本">
                <label for="paramsbrowserv">浏览器版本</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.isDesktop" id="paramsisDesktop">
                    <option value="">不限</option>
                    <option value="true">是</option>
                    <option value="false">否</option>
                </select>
                <label for="paramsisDesktop">是否桌面设备</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.isTablet" id="paramsisTablet">
                    <option value="">不限</option>
                    <option value="true">是</option>
                    <option value="false">否</option>
                </select>
                <label for="paramsisTablet">是否平板设备</label>
            </div>
            <div class="mb-3 col-12 form-floating">
                <select class="form-select" v-model="params.isPhone" id="paramsisPhone">
                    <option value="">不限</option>
                    <option value="true">是</option>
                    <option value="false">否</option>
                </select>
                <label for="paramsisPhone">是否移动设备</label>
            </div>
        </form>
    </x-offcanvas>
    
    <!--有操作记录，length代表公告的条数-->
    <div v-if="operations.length>0">
        <!--点击操作记录后的弹出拟态框-->
        <x-modal id='info' title="操作信息">
            <div v-if="operation!==null" style="word-wrap:break-word;word-break:break-all;over-flow:hidden;">
                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">#@{{ operation.oid }}</span>
                            <a class="form-control btn btn-outline-dark" title="打开用户主页" :href="'/user/'+operation.uid" target="_blank">@{{ "#"+operation.uid+" "+operation.uname}}</a>
                            <input class="form-control btn btn-outline-dark" title="复制该IP" onclick="copy(this)" :value="operation.oip"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">@{{ operation.otime }}</span>
                            <a class="form-control btn btn-outline-dark" title="筛选操作类型" @click="set('type',operation.otype)">@{{ otypes[operation.otype] }}</a>
                        </div>
                    </div><hr/>
                    <div class="col-12">
                        <div class="input-group">
                            <a class="form-control btn btn-outline-dark" title="筛选操作系统" @click="set('platform',operation.oinfo.platform)"><i class="bi" :class="{'bi-android2':operation.oinfo.platform==='AndroidOS','bi-microsoft':operation.oinfo.platform==='Windows','bi-apple':operation.oinfo.platform==='iOS'}"></i> @{{ operation.oinfo.platform }}</a>
                            <a class="form-control btn btn-outline-dark" title="筛选操作系统版本" @click="set('platformv',operation.oinfo.platformv)">V @{{ operation.oinfo.platformv }}</a>
                            <a class="form-control btn btn-outline-dark" title="筛选设备" @click="set('device',operation.oinfo.device)">@{{ operation.oinfo.device }}</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <a class="form-control btn btn-outline-dark" title="筛选浏览器" @click="set('browser',operation.oinfo.browser)"><i class="bi" :class="['bi-browser-'+operation.oinfo.browser.toLowerCase()]"></i> @{{ operation.oinfo.browser }}</a>
                            <a class="form-control btn btn-outline-dark" title="筛选浏览器版本" @click="set('browserv',operation.oinfo.browserv?operation.oinfo.browserv:'')">@{{ (operation.oinfo.browserv?'V '+operation.oinfo.browserv:"无") }}</a>
                        </div>
                    </div><hr/>
                    <div class="col-12 input-group">
                        <span class="input-group-text">请求体</span>
                        <span class="form-control">@{{Object.keys(operation.orequest).length}} 项</span>
                    </div>
                    <div v-for="(item,index) in operation.orequest" class="col-12 input-group">
                        <span class="input-group-text" v-text="index"></span>
                        <input disabled class="form-control" :value="item"/>
                    </div>
                    <hr/>
                    <div class="col-12 input-group">
                        <span class="input-group-text">结果</span>
                        <span class="form-control">@{{Object.keys(operation.oresult).length}} 项</span>
                    </div>
                    <div v-for="(item,index) in operation.oresult" class="col-12 input-group">
                        <span class="input-group-text" v-text="index"></span>
                        <input disabled class="form-control" :value="item"/>
                    </div><hr/>
                </div>
            </div>
        </x-modal>
        <div class="item thead-dark thead">
            <div class="row">
                <div class="col-12 text-center row align-items-center">
                    <div class="col-1"><a class="btn btn-light btn-fill" @click="set('desc')" :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'"># <i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></a></div>
@if (isset($utype)&&$utype==='a')
                    <div class="col-2"><a title="所有用户" class="btn btn-fill btn-light" @click="set('uid','')">用户</a></div>
@else
                    <div class="col-2"><a class="btn btn-fill btn-light">用户</a></div>
@endif
                    <div class="col-2"><a title="所有IP" class="btn btn-fill btn-light" @click="set('oip','')">IP</a></div>
                    <div class="col-3"><a title="所有操作类型" class="btn btn-fill btn-light" @click="set('type','')">操作</a></div>
                    <div class="col-2"><a title="所有结果类型" class="btn btn-fill btn-light" @click="set('status','')">结果</a></div>
                    <div class="col-2"><a :title="params.desc==='0'?'正序【从小到大】':'倒序【从大到小】'" class="btn btn-fill btn-light" @click="set('desc')">时间 <i class="bi" :class="{'bi-sort-up-alt':params.desc==='0','bi-sort-up':params.desc==='1'}" ></i></a></div>
                </div>
            </div>
        </div>
        <div class="item" v-for="(operation,index) in operations" style="display: flex;" :key="index" >
            <div class="row text-center col-12 align-items-center" title="双击查看操作信息" @dblclick="openinfo(index)">
                <div class="col-1 thead">@{{ operation.oid }}</div>
                <div class="col-2"><a :title="'筛选用户：#'+operation.uid+' '+operation.uname" @click="set('uid',operation.uid)" class="btn btn-fill btn-light">@{{operation.uname}}</a></div>
                <div class="col-2 text-truncate"><a :title="'筛选IP：'+operation.oip" @click="set('oip',operation.oip)" class="btn btn-fill btn-light">@{{ operation.oip }}</a></div>
                <div class="col-3"><a :title="'筛选操作类型：'+otypes[operation.otype]" @click="set('type',operation.otype)" class="btn btn-fill btn-light">@{{otypes[operation.otype]}}</a></div>
                <div class="col-2"><a :title="'筛选结果状态：'+statuses[operation.oresult.status].label" @click="set('status',operation.oresult.status)" class="btn btn-fill" :class="['btn-outline-'+statuses[operation.oresult.status].btn]">@{{statuses[operation.oresult.status].label}}</a></div>
                <div class="col-2" style="font-size:x-small;">@{{ time.otime[index].status+(time.otime[index].length!==null?"前":"") }}</div>
            </div>
        </div>
        <!--分页-->
        @include('template.paginator')
    </div>
    <p v-if="operations.length==0">抱歉，查询不到任何操作！</p>

</main>

<script>
    const operationlist=Vue.createApp({
        data(){
            return{
                time:{otime:[]},
                dataname:"operations",
                paginator:{},
                pagenum:{{ $config_operation['pagenum'] }},
                operations:[],
                operation:null,
                url:"{{ config('var.sgo') }}",
                otypes:{!! json_encode($config_operation['type']) !!},
                statuses:{!! json_encode($config_operation['status']) !!},
                paramspre:{},
                params:{
                    page:"1",
                    oid:"",
                    oip:"",
@if (isset($utype)&&$utype==='a')
                    uid:"",
@else
                    uid:{{isset($luser)?$luser->uid:''}},
@endif
                    status:"",
                    type:"",
                    ostart:"2023-01-01T00:00:00",
                    oend:"2023-12-31T00:00:00",
                    device:"",
                    browser:"",
                    browserv:"",
                    platform:"",
                    platformv:"",
                    isDesktop:"",
                    isTablet:"",
                    isPhone:"",
                    order:"oid",
                    desc:"1",
                },
                ordertypes:{
                    oid:"按操作编号排序",
                },
            }
        },
        mounted(){
            //初始化分页
            initpaginator(this);
            this.getData();
        },
        computed:{
        },
        methods:{
            initStatus(){
                initStatus(this,"otime");
            },
            //打开信息
            openinfo(index){
                this.operation = this.operations[index];
                $('#info').modal("show");
            },

            //得到最后一次更新的时间
            getLastTime(index){
                return getSubTime(getDate(this.operations[index].otime).getTime(),(new Date()).getTime());
            },
            //重置数据
            reset(){
                this.params=this.paramspre={
                    page:"1",
                    oid:"",
                    oip:"",
@if (isset($utype)&&$utype==='a')
                    uid:"",
@else
                    uid:{{isset($luser)?$luser->uid:''}},
@endif
                    status:"",
                    type:"",
                    ostart:"2023-01-01T00:00:00",
                    oend:"2023-12-31T00:00:00",
                    device:"",
                    browser:"",
                    browserv:"",
                    platform:"",
                    platformv:"",
                    isDesktop:"",
                    isTablet:"",
                    isPhone:"",
                    order:"oid",
                    desc:"1",
                };
            },
        }
    }).mount('#operationlist');
</script>