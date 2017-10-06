<?php
namespace api\models;

use common\models\User;
use yii\base\Model;

class LoginForm extends Model
{
    /**
     *  E-mail пользователя
     * @var string
     */
    public $email;

    /**
     * пароль пользователя
     * @var string
     */
    public $password;

    /**
     * Сгенерировать новый токен
     * @var bool
     */
    public $resetToken = false;

    /**
     * Пользователь по [[email]]
     * @var null | User
     */
    protected $_User = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'string'],
            ['resetToken', 'boolean'],
            ['password', 'checkPassword'],
        ];
    }

    /**
     * Проверка пароля
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @return boolean
     */
    public function checkPassword($attribute, $params)
    {
        $ret = false;
        if (!$this->hasErrors()) {
            $User = $this->getUser();
            if ($User && $User->validatePassword($this->password)) {
                $ret = true;
            } else {
                $this->addError($attribute, "Неверные пользователь или пароль");
            }
        }
        return $ret;
    }

    /**
     * Получить объект пользователя по email
     * @return null | User
     */
    protected function getUser()
    {
        $ret = User::findOne(['email' => $this->email]);

        return $ret;
    }

    /**
     * Войти по данным [[email]] и [[password]]
     * @return false|string Если успешно, то вернуть restToken
     */
    public function auth()
    {
        $ret = false;
        do {

            if (!$this->validate()) {
                break; // exit do-while
            }
            $User = $this->getUser();
            if ($this->resetToken || !$User->restToken) {
                $User->generateRestToken();
                if (!$User->save()) {
                    \Yii::info(\yii\helpers\VarDumper::dumpAsString($User->getErrors()));
                    break; // exit do-while
                }
            }
            $ret = $User->restToken;

        } while(false);

        return $ret;
    }
}