<?php

namespace common\models\query;

use common\components\DbActiveQuery;

/**
 * This is the ActiveQuery class for [[Entity]].
 *
 * @see Entity
 */
class EntityQuery extends DbActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Entity[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Entity|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
