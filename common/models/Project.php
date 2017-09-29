<?php

namespace common\models;

use Yii;

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
class Project extends \yii\db\ActiveRecord
{
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
