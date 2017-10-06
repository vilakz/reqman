<?php
namespace api\controllers;

use api\models\Token;
use Yii;
use yii\rest\Controller;
use api\models\LoginForm;

class SiteController extends Controller
{
    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        return 'api';
    }

    /**
     * Получить токен для доступа
     * @return LoginForm|Token
     */
    public function actionToken()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if (false !== ($token = $model->auth())) {
            return new Token($token);
        } else {
            return $model;
        }
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'token' => ['post'],
            'index' => ['get'],
        ];
    }
}