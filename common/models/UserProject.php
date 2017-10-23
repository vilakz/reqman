<?php

namespace common\models;

use common\models\query\UserProjectQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "userProject".
 *
 * @property integer $id
 * @property integer $projectId
 * @property integer $userId
 * @property string $updatedAt
 *
 * @property Project $project
 * @property User $user
 */
class UserProject extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userProject';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projectId', 'userId'], 'required'],
            [['projectId', 'userId'], 'integer'],
            [['updatedAt'], 'safe'],
            [['projectId', 'userId'], 'unique', 'targetAttribute' => ['projectId', 'userId'], 'message' => 'The combination of Project ID and User ID has already been taken.'],
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projectId' => 'Project ID',
            'userId' => 'User ID',
            'updatedAt' => 'Updated At',
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * @inheritdoc
     * @return UserProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProjectQuery(get_called_class());
    }
}
