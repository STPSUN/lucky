<?php

namespace addons\fomo\model;

/**
 * @author shilinqing
 */
class KeyRecord extends \web\common\model\BaseModel{
    
    protected function _initialize() {
        $this->tableName = 'fomo_key_record';
    }
    
    public function getList($pageIndex = -1, $pageSize = -1, $filter = '', $order = 'update_time desc') {
        $g = new \addons\fomo\model\Game();
        $t = new \addons\fomo\model\Team();
        $u = new \addons\member\model\MemberAccountModel();
        $sql = 'select a.*,g.name as game_name,g.status,t.name as team_name,u.username from '.$this->getTableName().' a ,'.$g->getTableName().' g,'.$t->getTableName().' t,'.$u->getTableName().' u where a.user_id=u.id and a.game_id=g.id and a.team_id=t.id';
        if($filter!=''){
            $sql = 'select * from ('.$sql.') as tab where '.$filter;
        }
        return $this->getDataListBySQL($sql, $pageIndex, $pageSize, $order);
    }
    
    public function getTotal($filter = '') {
        $g = new \addons\fomo\model\Game();
        $t = new \addons\fomo\model\Team();
        $u = new \addons\member\model\MemberAccountModel();
        $sql = 'select a.*,g.name as game_name,g.status,t.name as team_name,u.username from '.$this->getTableName().' a ,'.$g->getTableName().' g,'.$t->getTableName().' t,'.$u->getTableName().' u where a.user_id=u.id and a.game_id=g.id and a.team_id=t.id';
        if($filter!=''){
            $sql = 'select count(*) as c from ('.$sql.') as tab where '.$filter;
        }
        $count = $this->query($sql);
        return $count[0]['c'];
    }
    
    public function getCountTotal($filter = '') {
        $u = new \addons\member\model\MemberAccountModel();
        $sql = 'select a.*,u.username from '.$this->getTableName().' a ,'.$u->getTableName().' u where a.user_id=u.id';
        if($filter!=''){
            $sql = 'select sum(key_num) as count_total from ('.$sql.') as tab where '.$filter;
        }
        $count = $this->query($sql);
        return $count[0]['count_total'];
    }

    
    public function saveUserKey($user_id,$team_id,$game_id, $key_num, $eth, $is_limit = true){
        $where['user_id'] = $user_id;
        $where['team_id'] = $team_id;
        $where['game_id'] = $game_id;
        $data = $this->where($where)->find();
        $bonus_limit_num = 0;
        if($is_limit){
            $m = new \addons\member\model\MemberAccountModel();
            $bonus_limit = $m->where('id='.$user_id)->value('bonus_limit');
            if($bonus_limit > 0){
                $bonus_limit_num = $eth * $bonus_limit; // 投注额 * 封顶限制倍数
            }
        }
        if(!empty($data)){
            $data['before_num'] = $data['key_num'];
            $data['key_num'] = $data['key_num'] + $key_num;
            $data['eth'] = $data['eth'] + $eth;
            $data['bonus_limit_num'] = $data['bonus_limit_num'] + $bonus_limit_num;
            $data['update_time'] = NOW_DATETIME;
            return $this->save($data);
        }else{
            $data['team_id'] = $team_id;
            $data['game_id'] = $game_id;
            $data['user_id'] = $user_id;
            $data['key_num'] = $key_num;
            $data['eth'] = $eth;
            $data['bonus_limit_num'] = $bonus_limit_num;
            $data['update_time'] = NOW_DATETIME;
            return $this->add($data);
        }
    }
    
    /**
     * 获取当场游戏用户key总量
     * @param type $user_id
     * @param type $game_id
     * @return type
     */
    public function getTotalByGameID($user_id,$game_id){
        $where['user_id'] = $user_id;
        $where['game_id'] = $game_id;
        $data = $this->where($where)->sum('key_num');
        if(empty($data)){
            return 0;
        }
        return $data;
    }

    /**
     * 获取user_id 以外的所有指定游戏,战队 - 拥有key的用户
     * @param type $user_id
     * @param type $game_id 
     */
    public function getRecordWithOutUserID($user_id, $game_id ,$team_id=''){
        if(!empty($team_id)){
            $where['team_id'] = $team_id;
        }
        $where['game_id'] = $game_id;
        $where['user_id'] = array('<>', $user_id);
        return $this->where($where)->field('id,user_id,sum(key_num) as key_num')->group('user_id')->select();
    }
    
    /**
     * 获取指定游戏,战队的key的总数 , 传入user_id参数为总数-当前购买用户user_id的key数量
     * @param type $game_id
     * @return int
     */
    public function getCrontabTotalByGameID($game_id,$team_id='',$user_id=''){
        if(!empty($team_id)){
            $where['team_id'] = $team_id;
        }
        if(!empty($user_id)){
            $where['user_id'] = array("<>",$user_id);
        }
        $where['game_id'] = $game_id;
        $data = $this->where($where)->sum('key_num');
        if (empty($data)) {
            return 0;
        }
        return $data;
    }
    
    /**
     * 获取最后一个投注者
     */
    public function getLastWinner($game_id){
        $m = new \addons\fomo\model\Game();
        $game = $m->getDetail($game_id);
        if($game['is_set_winner'] == 1){
            $data['user_id'] = $game['winner_user_id'];
            $data['team_id'] = $game['winner_team_id'];
        }else{
            //没有指定赢家
            $where['game_id'] = $game_id;
            $data = $this->where($where)->field('id,team_id,user_id')->order('update_time desc')->find();
        }
        return $data;
        
    }
    
    /**
     * 随机投放空投(根据用户购买key所花费的eth)
     */
    public function getRandUserID($game_id){
        $data = $this->where('game_id='.$game_id)->select();
        if(!empty($data)){
            $count = count($data);
            $rand = rand(0, $count-1);
            return $data[$rand];
        }else{
            return $data;
        }
        
    }
    
    public function getUserTotalEthByGameID($user_id,$game_id){
        $where['game_id'] = $game_id;
        $where['user_id'] = $user_id;
        return $this->where($where)->sum('eth');
    }
  
}
