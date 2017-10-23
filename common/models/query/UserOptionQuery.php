<?php

namespace common\models\query;

use common\components\DbActiveQuery;

/**
 * This is the ActiveQuery class for [[UserOption]].
 *
 * @see UserOption
 */
class UserOptionQuery extends DbActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserOption[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserOption|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
