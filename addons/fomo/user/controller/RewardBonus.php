<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace addons\fomo\user\controller;

/**
 * Description of RewardRecord
 * 分红列表
 * @author shilinqing
 */
class RewardBonus extends \web\user\controller\AddonUserBase{

    public function index(){
        $type = $this->_get('type');
        $status = $this->_get('status',0);
        if($type == '' || $type==0){
            $type = 0;
        }
        $m = new \addons\config\model\Coins;
        $coins = $m->getDataList(-1,-1,'','','id asc');
        $this->assign('coins',$coins);
        $this->assign('type',$type);
        $this->assign('status',$status);
        return $this->fetch();
    }

    public function loadList() {
        $keyword = $this->_get('keyword');
        $type = $this->_get('type', -1);
        $scene = $this->_get('scene', -1);
        $status = $this->_get('status', -1);
        $coin_id = $this->_get('coin_id', 0);
        $m = new \addons\fomo\model\BonusSequeue();
        $filter = '1=1 ';
        if( $type != -1){
            $filter .= ' and type='. $type;
        }
        if($status != -1){
            $filter .= ' and status='. $status;
        }
        if( $scene != -1){
            $filter .= ' and scene='. $scene;
        }
        if($coin_id && $coin_id !=0){
            $filter .= ' and coin_id='. $coin_id;
        }
        if ($keyword != null) {
            $filter .= ' and username like \'%' . $keyword . '%\'';
        }
        $total = $m->getTotal($filter);
        $rows = $m->getDataList($this->getPageIndex(), $this->getPageSize(), $filter,'*', 'id desc');
        $count_total = $m->getCountTotal($filter);
        return $this->toTotalDataGrid($total, $rows,$count_total);
    }




}
