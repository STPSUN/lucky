<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:92:"/Users/shilinqing/GitHub/lucky/public/../addons/fomo/user/view/default/key_record/index.html";i:1541827347;}*/ ?>


<div class="right-main">
    <div class="page_nav" id="js_page_nav"><span class="page_title"><?php echo $page_nav; ?></span></div>
    
    <div id="js_main_header" class="ui-form main_header">
        <ul class="tab_navs" id="js_tab_navs">
            <li class="<?php if($type == 0): ?>current<?php endif; ?>"><a class="pjax" href="<?php echo getUrl('index','type=0'); ?>">F3D</a></li>            
            <li class="<?php if($type == 1): ?>current<?php endif; ?>"><a class="pjax" href="<?php echo getUrl('index','type=1'); ?>">P3D</a></li>  
        </ul>
        <?php if($type == 0): ?>
        <span>
            <select name="game_id" id="game_id" class="form-control" style="width:130px">
                <option value="">指定游戏</option>
                <?php if(is_array($games) || $games instanceof \think\Collection || $games instanceof \think\Paginator): $i = 0; $__LIST__ = $games;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$game): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $game['id']; ?>"><?php echo $game['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </span>
        <span>
            <select name="team_id" id="team_id" class="form-control" style="width:130px">
                <option value="">指定战队</option>
                <?php if(is_array($teams) || $teams instanceof \think\Collection || $teams instanceof \think\Paginator): $i = 0; $__LIST__ = $teams;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$team): $mod = ($i % 2 );++$i;?>
                <option value="<?php echo $team['id']; ?>"><?php echo $team['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </span>
        <?php endif; ?>
        <span class="frm_input_box search append">
            <a href="javascript:void(0);" id="js_search" class="frm_input_append">
                <i class="icon wb-search" title="搜索"></i>
            </a>
            <input type="text" id="js_keyword" placeholder="请输入用户名称" value="" class="frm_input" />
        </span>
       
    </div>
    <table id="grid-table">
        <thead frozen="true">
        <th data-options="field:'username',width:100,align:'center'">用户名称</th> 
        </thead>
        <thead>
            <tr>
                <?php if($type == 0): ?>
                    <th data-options="field:'game_name',width:120, align:'center'">游戏名称</th>
                    <th data-options="field:'status',width:120, align:'center',formatter:formatGameStatus">游戏状态</th>
                    <th data-options="field:'team_name',width:120, align:'center'">战队名称</th>
                    <th data-options="field:'key_num',width:120, align:'center'">持有key数量</th>
                    <th data-options="field:'before_num',width:120, align:'center'">更新前key数量</th>
                    <th data-options="field:'bonus_limit_num',width:120, align:'center'">当前分红封顶金额</th>
                    <th data-options="field:'update_time',width:140, align:'center'">更新时间</th>
                    
                    <!--<th data-options="field:'_oper',width:120,halign:'center',formatter: formatOper">操作</th>-->
                <?php else: ?>
                    <!-- p3d -->
                    <th data-options="field:'token',width:120, align:'center'">持有令牌数量</th>
                    <th data-options="field:'before_token',width:120, align:'center'">更新前令牌数量</th>
                    <th data-options="field:'update_time',width:140, align:'center'">更新时间</th>
                <?php endif; ?>
            </tr>
        </thead>
    </table>
</div>



<script type="text/javascript">
    var type = "<?php echo $type; ?>";
    function formatOper(value, row, index) {
        if(row['id']){
            var html = '<span class="grid-operation">';
//            html += '<button type="button" onclick="setWinner(' + row['id'] + ')" class="btn btn-xs btn-default edit-btn"><i class="icon wb-edit"></i>设置游戏赢家</button>';
    //        html += '<button type="button" onclick="del(' + row['id'] + ')" class="btn btn-xs btn-default del-btn"><i class="icon wb-close"></i>删除</button>';
            html += '</span>';
            return html;
        }
    }
    
    function formatStatus(value,row,index){
        if(row['id']){
            var text = '<span style="color:red">否</span>';
            if(value == '1')
                text = '<span style="color:green">是</span>';
            return text;
        }
    }
    //    游戏状态：0=未开始，1=已开始，2=已结束'
    function formatGameStatus(value,row,index){
        if(row['id']){
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
            url: getURL('loadList',"type="+type),
            method: "GET",
            height: getGridHeight(),
            rownumbers: true,
            singleSelect: true,
            remoteSort: false,
            multiSort: true,
            emptyMsg: '<span>无相关数据</span>',
            pagination: true,
            showFooter: true,
            pageSize: 20,
            onLoadSuccess: function (data) {
                
                $('#grid-table').datagrid('reloadFooter', [
                    {
                        <?php if($type == 0): ?>
                        team_name: '统计',
                        key_num: data.count_total,
                        <?php else: ?>
                        username:'统计',
                        token:data.count_total
                        <?php endif; ?>
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
    
    function setWinner(id) {
        confirm("同局多次设置赢家以最后一次设置的为准,确认要设置此用户为本局赢家吗?", function () {
            var url = getURL('set_winner');
            $.getJSON(url, {id: id}, function (json) {
                if (json.success){
                    reload();
                }
                else
                    alert(json.message);
            });
        });
    }


    $("#js_search").click(function () {
        reload();
    });
    function reload() {
        var keyword = $("#js_keyword").val();
        var game_id = $("#game_id").val();
        var team_id = $("#team_id").val();
        $('#grid-table').datagrid('reload', {keyword: keyword,game_id:game_id,team_id:team_id});
    }
    
    $("#game_id").change(function () {
        reload()
    });
    
    $("#team_id").change(function () {
        reload()
    });
    
</script>
