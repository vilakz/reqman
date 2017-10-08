<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use yii\helpers\VarDumper;

class MiscController extends Controller
{

    /**
     * Создание роли админа, если её ещё нет
     * @return int
     */
    public function actionCreateAdmin()
    {
        $ret = 0;
        $auth = \Yii::$app->authManager;
        $admins = $auth->getUserIdsByRole('administrator');
        if (!count($admins)) {
            $User = new User();
            $User->setPassword('111111');
            $User->generateAuthKey();
            $userName = 'admin@reqman.test';
            $User->username = $userName;
            $User->email = $userName;

            if (!$User->save()) {
                $this->stdout("user create failed [".\yii\helpers\VarDumper::dumpAsString($User->getErrors())."]");
                $ret = 1;
            } else {
                $auth->assign($auth->getRole('administrator'), $User->id);
            }
        }

        return $ret;
    }

    /**
     * Создание нескольких пользователей
     */
    public function actionCreateUsers()
    {
        $arr = [
            ['name' => 'admin',         'email' => 'admin1@t.t',         'role' => 'administrator',         'password' => '111111'],
            ['name' => 'projectAdmin',  'email' => 'projectAdmin1@t.t',  'role' => 'projectAdministrator',  'password' => '111111'],
            ['name' => 'projectEditor', 'email' => 'projectEditor1@t.t', 'role' => 'projectEditor',         'password' => '111111'],
            ['name' => 'projectViewer', 'email' => 'projectViewer@t.t',  'role' => 'projectViewer',         'password' => '111111'],
        ];

        $auth = \Yii::$app->authManager;
        foreach($arr as $item) {
            $User = new User();
            $User->setPassword($item['password']);
            $User->generateAuthKey();
            $User->username = $item['email'];
            $User->email = $item['email'];

            if (!$User->save()) {
                $this->stdout("user create failed [".\yii\helpers\VarDumper::dumpAsString($User->getErrors())."]");
                break;
            } else {
                $Role = $auth->getRole($item['role']);
                $auth->assign($Role, $User->id);
            }
        }
    }

    public function actionTestDb()
    {
        $db = Yii::$app->db;
        echo VarDumper::dumpAsString(compact('db'), 3);
        $res = $db->createCommand("show VARIABLES LIKE 'version_comment'")->queryOne();
        echo VarDumper::dumpAsString(compact('res'));
    }

}