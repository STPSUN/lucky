<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace addons\fomo\index\controller;

/**
 * Description of Crontab
 * f3d投注分配限制所选战队, p3d为所有
 * 类型：0=p3d分红,1=f3d分红
 * @author shilinqing
 */
class Crontab extends \web\common\controller\Controller {

    public function _initialize() {
        
    }

    public function excute() {
        $id = $this->_get('id');
        $queueM = new \addons\fomo\model\BonusSequeue();
        $data = $queueM->getUnSendData($id);
        if (!empty($data)) {
            try {
                $queueM->startTrans();
                if ($data['type'] == 1) {
                    //f3d分红,去除用户本身
                    $res = $this->sendF3d($data['user_id'], $data['coin_id'], $data['game_id'], $data['amount'], $data['scene'], $data['team_id']);
                } else {
                    //p3d分红
                    $res = $this->sendP3d($data['user_id'], $data['coin_id'], $data['amount'], $data['game_id'], $data['scene']);
                }
                if ($res) {
                    //更新发放状态
                    $data['status'] = 1;
                    $data['update_time'] = NOW_DATETIME;
                    $queueM->save($data);
                    $queueM->commit();
                    return json($this->successData());
                } else {
                    return json($this->failData('发放失败'));
                }
            } catch (\Exception $ex) {
                $queueM->rollback();
                return json($this->failData($ex->getMessage()));
            }
        }else{
            return $this->successData();
        }
    }
    
    public function excuteAll(){
        set_time_limit(0);
        $queueM = new \addons\fomo\model\BonusSequeue();
        $sequeue_list = $queueM->getUnAllSendData(1000);
//        dump($sequeue_list);exit;
        if (!empty($sequeue_list)) {
            foreach($sequeue_list as $k => $data){
                try {
                    $queueM->startTrans();
                    if ($data['type'] == 1) {
                        //f3d分红,去除用户本身
                        $res = $this->sendF3d($data['user_id'], $data['coin_id'], $data['game_id'], $data['amount'], $data['scene'], $data['team_id']);
                    } else {
                        //p3d分红
                        $res = $this->sendP3d($data['user_id'], $data['coin_id'], $data['amount'], $data['game_id'], $data['scene']);
                    }
                    if ($res) {
                        //更新发放状态
                        $data['status'] = 1;
                        $data['update_time'] = NOW_DATETIME;
                        $queueM->save($data);
                        $queueM->commit();
                    } else {
                        $queueM->rollback();
                    }
                } catch (\Exception $ex) {
                    $queueM->rollback();
                }
            }
            echo '队列处理成功';
        }else{
            echo '队列处理成功';
        }
    }

    /**
     * 代理奖励发放
     */
    public function agencyAward()
    {
        $agencyAwardM = new \addons\fomo\model\AgencyAward();
        $balanceM = new \addons\member\model\Balance();
        $rewardM = new \addons\fomo\model\RewardRecord();
        $data = $agencyAwardM->where('status',1)->limit(1000)->select();
        foreach ($data as $v)
        {
            $user_id = $v['user_id'];
            $amount = $v['amount'];
            $coin_id = $v['coin_id'];
            $game_id = $v['game_id'];
            $remark = '代理分红';
            $type = 5;
            //添加余额, 添加分红记录
            $balance = $balanceM->updateBalance($user_id, $amount, $coin_id, true);
            if ($balance != false) {
                $before_amount = $balance['before_amount'];
                $after_amount = $balance['amount'];
                $rewardM->addRecord($user_id, $coin_id, $before_amount, $amount, $after_amount, $type, $game_id, $remark);
            }
        }
    }

    /**
     * 发放F3d分红
     * @param type $user_id
     * @param type $coin_id
     * @param type $game_id
     * @param type $amount
     * @param type $scene 场景id 场景:0=p3d购买，1=f3d投注分配，2=f3d开奖分配'
     * @param type $team_id 
     */
    private function sendF3d($user_id, $coin_id, $game_id, $amount, $scene, $team_id) {
        $keyRecordM = new \addons\fomo\model\KeyRecord();
        $balanceM = new \addons\member\model\Balance();
        $rewardM = new \addons\fomo\model\RewardRecord();
        if ($scene == 2) {
            //开奖分红 查询战队成员key总数 and record
            $record_list = $keyRecordM->getRecordWithOutUserID($user_id, $game_id, $team_id);
            $total_key = $keyRecordM->getCrontabTotalByGameID($game_id, $team_id,$user_id);
            $type = 1; //1=胜利战队分红
            $remark = '胜利战队分红';
        } else {
            //查询拥有key的所有user
            $record_list = $keyRecordM->getRecordWithOutUserID($user_id, $game_id);
            $total_key = $keyRecordM->getCrontabTotalByGameID($game_id,'',$user_id);
            $type = 0; //奖励类型 0=投注分红
            $remark = '欲望之岛投注分红';
        }
        if (!empty($record_list)) {
            foreach ($record_list as $k => $record) {
                $user_id = $record['user_id'];
                $key_num = $record['key_num'];
                $rate = $this->getUserRate($total_key, $key_num);
                $_amount = $amount * $rate;

                //封顶限制
                $bonus_limit_num = $record['bonus_limit_num'];
                if(!empty($bonus_limit_num))
                {
                    $profit_amount = $rewardM->where(['user_id' => $record['user_id'], 'type' => 0, 'game_id' => $game_id])->sum('amount');
                    $total_amount = $profit_amount + $_amount;
                    if($profit_amount >= $bonus_limit_num)
                        continue;

                    if($total_amount >= $bonus_limit_num)
                        $_amount = $total_amount - $bonus_limit_num;
                }

                //添加余额, 添加分红记录
                $balance = $balanceM->updateBalance($user_id, $_amount, $coin_id, true);
                if ($balance != false) {
                    $before_amount = $balance['before_amount'];
                    $after_amount = $balance['amount'];
                    $rewardM->addRecord($user_id, $coin_id, $before_amount, $_amount, $after_amount, $type, $game_id, $remark);
                }
            }
        }
        return true;
    }

    /**
     * 发放P3d分红 
     * @param type $coin_id
     * @param type $amount
     * @param type $game_id
     * @param type $scene 场景id 场景:0=p3d购买，1=f3d投注分配，2=f3d开奖分配'
     * @return boolean
     */
    private function sendP3d($user_id, $coin_id, $amount, $game_id, $scene) {
        $tokenRecordM = new \addons\fomo\model\TokenRecord();
        $balanceM = new \addons\member\model\Balance();
        $rewardM = new \addons\fomo\model\RewardRecord();
        $total_amount = $tokenRecordM->getTotalToken(); //p3d总额
        if($scene == 0){
            $user_token = $tokenRecordM->getTotalToken($user_id);
            $total_amount = $total_amount - $user_token;
        }
        $filter = '';
        if ($scene == 0) {
            //场景:0=p3d购买
            $filter = 'user_id !=' . $user_id;
        }
        $user_list = $tokenRecordM->getDataList(-1, -1, $filter, 'id,user_id,token', 'id asc');
        if (!empty($user_list)) {
            foreach ($user_list as $k => $user) {
                if ($user['token'] <= 0) {
                    continue;
                }
                $user_id = $user['user_id'];
                $rate = $this->getUserRate($total_amount, $user['token']);
                $_amount = $amount * $rate; //所得分红
                //添加余额, 添加分红记录
                $balance = $balanceM->updateBalance($user_id, $_amount, $coin_id, true);
                if ($balance != false) {
                    $before_amount = $balance['before_amount'];
                    $after_amount = $balance['amount'];
                    $type = 0; //奖励类型 0=投注分红，1=胜利战队分红，2=胜利者分红，3=邀请分红
                    $remark = '福利之岛投注分红';
                    $rewardM->addRecord($user_id, $coin_id, $before_amount, $_amount, $after_amount, $type, $game_id, $remark);
                }
            }
        }
        return true;
    }

    /**
     * 计算用户所拥有的key/token 数量占全部的百分比
     * @param type $total_amount
     * @param type $amount
     * @return type
     */
    private function getUserRate($total_amount, $amount) {
        return $amount / $total_amount;
    }

}
