<?php

namespace common\models\query;

use common\components\DbActiveQuery;

/**
 * This is the ActiveQuery class for [[Requirement]].
 *
 * @see Requirement
 */
class RequirementQuery extends DbActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Requirement[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Requirement|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
