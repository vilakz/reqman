<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $passwordHash
 * @property string $passwordResetToken
 * @property string $email
 * @property string $authKey
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 * @property string $restToken
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * Просмотр проекта
     */
    const PROJECT_TYPE_VIEWER = 'projectViewer';

    /**
     * Работа с проектом
     */
    const PROJECT_TYPE_EDITOR = 'projectEditor';

    /**
     * Администратор проекта
     */
    const PROJECT_TYPE_ADMIN = 'projectAdministrator';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['restToken', 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Имя пользователя',
            'passwordHash' => 'Хеш пароля',
            'passwordResetToken' => 'Токен сброса пароля',
            'email' => 'E-mail',
            'authKey' => 'Ключ аунтификации',
            'status' => 'Статус',
            'createdAt' => 'Время создания',
            'updatedAt' => 'Время обновления',
            'restToken' => 'Токен доступа для REST',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['restToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    /**
     * Получить список для Nav для смены пользователя (вход) на сайте. Отладочный метод.
     * @return array
     */
    public static function getUserLoginNav()
    {
        $ret = [];

        $users = static::find()
            ->select(['username', 'id']) // важен порядок следования
            ->indexBy('id')
            ->asArray()
            ->column();
        $auth = Yii::$app->authManager;
        foreach($users as $id => $username) {
            $roles = $auth->getRolesByUser($id);
            $names = \yii\helpers\ArrayHelper::getColumn($roles, 'name');
            $ret[] = ['label' => implode(',', $names)." ({$username})", 'url' => ['site/login-as', 'userId' => $id]];
        }

        return $ret;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProject()
    {
        return $this->hasMany(UserProject::className(), ['userId' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['id' => 'projectId'])
            ->via('userProject');
    }

    /**
     * Пользователь участвует в проекте ?
     * @param $projectId integer Id проекта
     * @return bool
     */
    public function isInProject($projectId)
    {
        $projects = $this->getProjects()->select(['id'])->column();
        return in_array($projectId, $projects);
    }

    /**
     * Получить список прав доступа к проекту
     * @return array
     */
    public static function getProjectTypeList()
    {
        $ret = [
            static::PROJECT_TYPE_VIEWER => 'Просмотр проекта',
            static::PROJECT_TYPE_EDITOR => 'Работа с проектом',
            static::PROJECT_TYPE_ADMIN => 'Администратор проекта',
        ];
        return $ret;
    }

    /**
     * Получить все роли пользователя через точку с запятой
     * @return string
     */
    public function getUserRolesText()
    {
        $roles = Yii::$app->authManager->getRolesByUser($this->id);
        return implode(';', \yii\helpers\ArrayHelper::getColumn($roles, 'name'));
    }

    public function getProjectRights()
    {
        return $this->getUserRolesText();
    }

    /**
     * Получить список проектов пользователя, где ключ - id, значение - название проекта
     * @return array
     */
    public function getProjectUserList()
    {
        $projects = $this->getProjects()->select(['name', 'id'])->indexBy('id')->column();
        return $projects;
    }

    /**
     * Записать в [[restToken]] случайный токен
     */
    public function generateRestToken()
    {
        $this->restToken = Yii::$app->security->generateRandomString(50 - 1 - 14). '_' . date("YmdHis");
    }

}
