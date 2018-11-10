<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:95:"/Users/shilinqing/GitHub/lucky/public/../addons/fomo/user/view/default/reward_record/index.html";i:1541827347;}*/ ?>


<div class="right-main">
    <div class="page_nav" id="js_page_nav"><span class="page_title"><?php echo $page_nav; ?></span></div>

    <div id="js_main_header" class="ui-form main_header">
        <span>
            <select name="coin_id" id="coin_id" class="form-control" style="width:130px">
                <option value="0">选择币种</option>
                <?php if(is_array($coins) || $coins instanceof \think\Collection || $coins instanceof \think\Paginator): $i = 0; $__LIST__ = $coins;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$game): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $game['id']; ?>"><?php echo $game['coin_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </span>
        <span>
            <select name="type" id="type" class="form-control" style="width:130px">
                <option value="-1">选择分红类型</option>
                <option value="0">投注分红</option>
                <option value="1">战队分红</option>
                <option value="2">胜利分红</option>
                <option value="3">邀请分红</option>
            </select>
        </span>

        <span class="frm_input_box search append">
            <a href="javascript:void(0);" id="js_search" class="frm_input_append">
                <i class="icon wb-search" title="搜索"></i>
            </a>
            <input type="text" id="js_keyword" placeholder="请输入名称" value="" class="frm_input"/>
        </span>
    </div>
    <table id="grid-table">
        <thead frozen="true">
        <th data-options="field:'username',width:100,align:'center'">用户名称</th>
        </thead>
        <thead>
        <tr>
            <th data-options="field:'coin_name',width:120, align:'center'">分红币种</th>
            <th data-options="field:'game_name',width:120, align:'center'">所属游戏</th>
            <th data-options="field:'status',width:100, align:'center',formatter:formatStatus">游戏状态</th>
            
            <th data-options="field:'amount',width:160, align:'center'">分红值</th>
            <th data-options="field:'remark',width:140, align:'center'">分红类型</th>
            <th data-options="field:'update_time',width:140, align:'center'">更新时间</th>
        </tr>
        </thead>
    </table>
</div>



<script type="text/javascript">
    var type = "<?php echo $type; ?>";
    function formatStatus(value,row,index){
        if(row['game_id'] !=0){
            var text = '未开始'
            if(value == '1')
                text = '<span style="color:green">进行中</span>';
            if(value == '2')
                text= '<span style="color:red">已结束</span>'
            return text;
        }
    }
    
    $(function () {
        $('#grid-table').datagrid({
            url: getURL('loadList', "type=" + type),
            method: "GET",
            height: getGridHeight(),
            rownumbers: true,
            singleSelect: true,
            remoteSort: false,
            multiSort: true,
            emptyMsg: '<span>无相关数据</span>',
            pagination: true,
            showFooter:true,
            pageSize: 20,
            onLoadSuccess:function(data) {
                $('#grid-table').datagrid('reloadFooter', [
                    {
                        coin_name: '统计',
                        amount: data.count_total
                    }
                ]);
            }
        });
        //设置分页控件 
        $('#grid-table').datagrid('getPager').pagination({
            pageSize: 20, //每页显示的记录条数，默认为10 
            pageList: [20, 30, 50]
        });
    });

    $("#js_search").click(function () {
        reload();
    });

    function reload() {
        var keyword = $("#js_keyword").val();
        var type = $("#type").val();
        var coin_id = $("#coin_id").val();
        $('#grid-table').datagrid('reload', {keyword: keyword, type: type, coin_id: coin_id});
    }

    $("#coin_id").change(function () {
        reload()
    });

    $("#type").change(function () {
        reload()
    });

</script>
