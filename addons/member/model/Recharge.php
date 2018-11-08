<?php
/**
 * Created by PhpStorm.
 * User: SUN
 * Date: 2018/11/8
 * Time: 15:56
 */

namespace addons\member\model;


class Recharge  extends \web\common\model\BaseModel
{
    public function _initialize()
    {
        $this->tableName = 'member_recharge';
    }
}