<?php

namespace common\models;

use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "userOption".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $projectId
 * @property string $name
 * @property string $value
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Project $project
 * @property User $user
 */
class UserOption extends \yii\db\ActiveRecord
{

    /* Текущий проект во всех фильтрах */
    const NAME_ACTIVE_PROJECT = 'activeProject';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'userOption';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'name', 'value'], 'required'],
            [['userId', 'projectId'], 'integer'],
            [['value'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['name'], 'string', 'max' => 30],
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
            'userId' => 'Пользователь',
            'projectId' => 'Проект',
            'name' => 'Название',
            'value' => 'Значение',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    /**
     * Получить значение опции
     * @param string $name
     * @param null | integer $projectId
     * @return mixed|string
     */
    public static function getOption($name, $projectId = null)
    {
        /* @var  User $User */
        $User = Yii::$app->user->identity;
        $UserOptionQuery = UserOption::find()
            ->andWhere(['userId' => $User->id])
            ;
        if ($projectId) {
            // только свои проекты
            $projectId = ($User->isInProject($projectId) ? $projectId : null);
        }
        if ($projectId) {
            $UserOptionQuery->andWhere(['projectId' => $projectId]);
        }
        $UserOption = $UserOptionQuery->one();
        $ret = ($UserOption ? $UserOption->value : static::getOptionDefaultValue($name, $projectId));
        return $ret;
    }

    /**
     * Установить значение для опции
     * @param string $name
     * @param mixed $value
     * @param null | integer $projectId
     * @return bool
     */
    public static function setOption($name, $value, $projectId = null)
    {
        $ret = true;
        /* @var  User $User */
        $User = Yii::$app->user->identity;
        $UserOptionQuery = UserOption::find()
            ->andWhere(['userId' => $User->id])
        ;
        if ($projectId) {
            // только свои проекты
            $projectId = ($User->isInProject($projectId) ? $projectId : null);
        }
        if ($projectId) {
            $UserOptionQuery->andWhere(['projectId' => $projectId]);
        }
        $UserOption = $UserOptionQuery->one();
        if ($UserOption) {
            // такое значение уже есть
            if ($UserOption->value != $value) {
                // значения различаются - перезаписать
                $UserOption->value = $value;
                if (!$UserOption->save()) {
                    Yii::error("Can't save UserOption, name=[$name], projectId=[$projectId], id=[{$UserOption->id}], errors=[".VarDumper::dumpAsString($UserOption->getErrors())."]");
                    $ret = false;
                }
            }
        } else {
            $defaultValue = static::getOptionDefaultValue($name, $projectId);
            if ($defaultValue != $value) {
                $UserOption = new UserOption();
                $UserOption->name = $name;
                $UserOption->projectId = $projectId;
                $UserOption->value = $value;
                if (!$UserOption->save()) {
                    Yii::error("Can't save UserOption, name=[$name], projectId=[$projectId], errors=[".VarDumper::dumpAsString($UserOption->getErrors())."]");
                    $ret = false;
                }
            } else {
                // тут ничего делать не надо, если опция по-умолчанию, то запись в БД не нужна.
            }
        }
        return $ret;
    }

    /**
     * Получить значение опции по-умолчанию
     * @param string $name
     * @param null | integer $projectId
     * @return mixed
     */
    public static function getOptionDefaultValue($name, $projectId = null)
    {
        $ret = null;
        switch($name) {
            case static::NAME_ACTIVE_PROJECT:
                $ret = null;
                break;
        }
        return $ret;
    }

    /**
     * Получить список названий опций
     * @return array
     */
    public static function getOptionNames()
    {
        $ret = [
            static::NAME_ACTIVE_PROJECT => 'Текущий проект во всех фильтрах',
        ];
        return $ret;
    }

    /**
     * Вернуть человекопонятное название
     * @return string
     */
    public function getHumanName()
    {
        $names = static::getOptionNames();
        $ret = '';
        if (isset($names[$this->name])) {
            $ret = $names[$this->name];
        }
        return $ret;
    }
}
