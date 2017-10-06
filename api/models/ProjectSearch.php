<?php

namespace api\models;

class ProjectSearch extends \common\models\ProjectSearch
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 's';
    }

}