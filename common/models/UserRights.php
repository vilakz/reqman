<?php
namespace common\models;

class UserRights extends User
{

    public $projectRights;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['projectRights', 'in', 'range' => array_keys(User::getProjectTypeList())],
        ];
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $auth = \Yii::$app->authManager;
        $auth->revokeAll($this->id);
        $auth->assign($auth->getRole($this->projectRights), $this->id);
        return true;
    }

}
