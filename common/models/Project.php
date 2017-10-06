<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "project".
 *
 * @property integer $id
 * @property string $name
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Entity[] $entities
 * @property Requirement[] $requirements
 * @property UserProject[] $userProjects
 * @property User[] $users
 */
class Project extends ActiveRecord
{

    /**
     * Сценарий для обновления модели по REST
     */
    const SCENARIO_REST_UPDATE = 'restUpdate';

    /**
     * Сценарий для создания модели по REST
     */
    const SCENARIO_REST_CREATE = 'restCreate';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_REST_CREATE] = ['name'];
        $scenarios[static::SCENARIO_REST_UPDATE] = ['name'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'createdAt' => 'Время создания',
            'updatedAt' => 'Время изменения',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // добавить текущего пользователя в проект
        if ($insert && isset(Yii::$app->user->identity)) {
            $User = Yii::$app->user->identity;
            $User->link('projects', $this);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntities()
    {
        return $this->hasMany(Entity::className(), ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequirements()
    {
        return $this->hasMany(Requirement::className(), ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProjects()
    {
        return $this->hasMany(UserProject::className(), ['projectId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'userId'])->via('userProjects');
    }

}
