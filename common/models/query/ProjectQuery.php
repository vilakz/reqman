<?php

namespace common\models\query;

use common\components\DbActiveQuery;

/**
 * This is the ActiveQuery class for [[Project]].
 *
 * @see Project
 */
class ProjectQuery extends DbActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Project[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Project|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
