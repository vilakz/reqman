<?php
namespace common\models;


use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

class AddUser extends Model
{

    public $email;

    public $projectId;

    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['projectId'], 'integer'],
            ['type', 'default', 'value' => User::PROJECT_TYPE_VIEWER],
            ['type', 'in', 'range' => array_keys(User::getProjectTypeList())],
            [['projectId'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['projectId' => 'id']],
            ['projectId', 'checkProjectId'],
            ['email', 'checkEmail'],
        ];
    }

    /**
     * Проверка принадлежности к проекту текущего пользователя
     * @param string $attribute Имя атрибута
     * @param array $params
     * @return boolean
     */
    public function checkProjectId($attribute, $params)
    {
        $ret = false;
        if (\Yii::$app->user->can('administrator')){
            $ret = true;
        } else {
            // если у текущего пользователя нет привязки к этому проекту, то вернуть false
            $User = \Yii::$app->user->identity;
            $projects = $User->getProjects()->select(['id'])->column();
            $prid = $this->projectId;
            if (in_array($this->projectId, $projects)) {
                $ret = true;
            } else {
                $this->addError( 'email', "Нет доступа к этому проекту" );
            }
        }

        return $ret;
    }

    /**
     * Проверка что такой email уже есть в этом проекте
     * @param string $attribute Имя атрибута
     * @param array $params
     * @return boolean
     */
    public function checkEmail($attribute, $params)
    {
        $ret = false;
        $User = User::findOne(['email' => $this->email]);
        if ($User) {
            $projects = $User->getProjects()->select(['id'])->column();
            if (in_array($this->projectId, $projects)) {
                $this->addError( $attribute, "Такой email уже есть в этом проекте" );
            } else {
                $ret = true;
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-Mail',
            'projectId' => 'Проект',
            'type' => 'Права доступа',
        ];
    }

    /**
     * Добавить пользователя в проект
     * @return bool|string
     */
    public function addUser()
    {
        $ret = true;
        do {
            $password = true;
            
            // проверить что такой пользователь уже есть
            $User = User::findOne(['email' => $this->email]);
            if (!$User) {
                // создать пароль в переменную
                $password = Yii::$app->security->generateRandomString(6);

                // создать пользователя с email и правами
                $User = new User();
                $User->setPassword($password);
                $User->generateAuthKey();
                $User->username = $this->email;
                $User->email = $this->email;

                if (!$User->save()) {
                    $ret = "Не удалось создать пользователя, причина : [" . \yii\helpers\VarDumper::dumpAsString($User->getErrors()) . "]";
                    break; // break do-while
                } else {
                    $auth = Yii::$app->authManager;
                    $role = $auth->getRole($this->type);
                    if ($role) {
                        $auth->assign($role, $User->id);
                    } else {
                        $ret = "Не удалось создать пользователя, причина : [не найдена его роль]";
                        break; // break do-while
                    }
                }
            }

            // присоединить его к проекту
            $Project = Project::findOne(['id' => $this->projectId]);
            if (!$Project) {
                $ret = "Пользователь создан, но он не присоединён к проекту";
                break; // break do-while
            }
            $User->link('projects', $Project);

            // отправить письмо о регистрации
            $sendResult = Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'invite-html', 'text' => 'invite-text'],
                    compact('User', 'Project', 'password')
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' робот'])
                ->setTo($this->email)
                ->setSubject('Вы добавлены в проект ' . $Project->name)
                ->send();
            if (!$sendResult) {
                $ret = "Пользователь создан и присоединен к проекту, но письмо ему не удалось выслать";
                break; // break do-while
            }
        } while(false);
        return $ret;
    }

}