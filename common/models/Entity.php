<?php

namespace common\models;

use common\components\behaviors\TimestampBehavior;
use Yii;
use yii\db\ActiveRecord;
use common\models\query\EntityQuery;

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
class Entity extends ActiveRecord
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
        return 'entity';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
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
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_REST_CREATE] = ['name', 'description', 'path'];
        $scenarios[static::SCENARIO_REST_UPDATE] = ['name', 'description', 'path'];
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

    /**
     * @inheritdoc
     * @return EntityQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EntityQuery(get_called_class());
    }
}
