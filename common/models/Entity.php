<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "entity".
 *
 * @property integer $id
 * @property string $name
 * @property integer $projectId
 * @property string $description
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Project $project
 */
class Entity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'entity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['projectId'], 'integer'],
            [['description'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['path'], 'string', 'max' => 200],
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
            'description' => 'Описание',
            'createdAt' => 'Время создания',
            'updatedAt' => 'Время обновления',
            'path' => 'Путь',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
    }
}
