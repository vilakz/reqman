<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "requirement".
 *
 * @property integer $id
 * @property string $name
 * @property integer $projectId
 * @property string $body
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Project $project
 */
class Requirement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requirement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'projectId'], 'required'],
            [['projectId'], 'integer'],
            [['body'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['name', 'projectId'], 'unique', 'targetAttribute' => ['name', 'projectId'], 'message' => 'The combination of Name and Project ID has already been taken.'],
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
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
            'projectId' => 'Проект',
            'body' => 'Подробности',
            'createdAt' => 'Время создания',
            'updatedAt' => 'Время обновления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'projectId'])
            ->via('project');
    }
}
