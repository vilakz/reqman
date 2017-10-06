<?php
namespace common\controllers;

use common\models\AddUser;
use common\models\Project;
use common\models\User;
use Yii;
use yii\base\Controller;
use yii\base\DynamicModel;
use yii\web\NotFoundHttpException;

/**
 * Общие Project экшены для браузера и REST
 */
class ProjectOverallController
{

    /**
     * Правила доступа общие для браузера и REST
     * @return array
     */
    public static function getAccessRules()
    {
        return [
            [
                'actions' => ['index'],
                'allow' => true,
                'roles' => ['projectView'],
            ],
            [
                'actions' => ['view'],
                'allow' => true,
                'roles' => ['projectView'],
                'matchCallback' => function ($rule, $action) {
                    return static::isUserInIdProject();
                },
            ],
            [
                'actions' => ['create'],
                'allow' => true,
                'roles' => ['projectAdmin'],
            ],
            [
                'actions' => ['update', 'delete', 'add', 'unset-user'],
                'allow' => true,
                'roles' => ['projectAdmin'],
                'matchCallback' => function ($rule, $action) {
                    return static::isUserInIdProject();
                },
            ],
        ];
    }

    /**
     * Добавить пользователя к проекту
     * @param $Controller yii\base\Controller
     * @param $id integer Id проекта
     * @return array|AddUser
     */
    public static function actionAdd($Controller, $id)
    {
        $Project = static::findModel($id);
        $model = new AddUser();
        $model->projectId = $Project->id;
        $ret = [
            'project' => $Project,
            'result' => false,
            'addUser' => $model,
        ];

        $formName = ($Controller instanceof \yii\rest\Controller ? '' : (new \ReflectionClass($model))->getShortName());
        $model->load(Yii::$app->request->bodyParams, $formName);
        if ($model->validate()) {
            $ret['result'] = $model->addUser();
        }
        return $ret;
    }

    /**
     * Найти модель по Id
     * @param $id integer Id проекта
     * @return Project
     * @throws NotFoundHttpException
     */
    public static function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Удалить пользователя из проекта
     * @param $Controller Controller
     * @param $id integer Id проекта
     * @return array
     */
    public static function actionUnsetUser($Controller, $id)
    {
        $ret = [
            'result' => false,
            'message' => '',
        ];
        do {
            $model = DynamicModel::validateData(['email'], [['email', 'email']]);
            if ($Controller instanceof \yii\rest\Controller) {
                $model->load(Yii::$app->request->bodyParams, '');
            } else {
                $model->load(Yii::$app->request->get(), '');
            }
            if (!$model->validate()) {
                $ret['result'] = false;
                $ret['message'] = 'Неверные входные данные';
                break; // break do-while
            }

            $Project = Project::findOne(['id' => $id]);
            $User = User::findOne(['email' => $model->email]);
            if (!$Project || !$User) {
                $ret['result'] = false;
                $ret['message'] = 'Неверные входные данные';
                break; // break do-while
            }

            // пользователь в проекте ?
            // что такая связь уже есть, иначе будет исключение
            if (!$User->isInProject($Project->id)) {
                $ret['result'] = false;
                $ret['message'] = 'Неверные входные данные';
                break; // break do-while
            }

            // проверить что проект принадлежит текущему пользователю
            if (!Yii::$app->user->can('administrator')) {
                $projects = Yii::$app->user->identity->getProjects()->select(['id'])->column();
                if (!in_array($Project->id, $projects)) {
                    $ret['result'] = false;
                    $ret['message'] = 'Недостаточно прав';
                    break; // break do-while
                }
            }
            // удалить из проекта
            $User->unlink('projects', $Project, true);

            $ret['result'] = true;

        } while (false);
        return $ret;
    }

    /**
     * Проверка что текущий пользователь состоит в проекте, где id проекта в HTTP запросе id
     * @return bool
     */
    public static function isUserInIdProject()
    {
        $ret = false;
        if (!Yii::$app->user->can('administrator')) {
            // проверить что пользователь входит в проект
            $id = Yii::$app->request->get('id');
            if ($id) {
                $Project = Project::findOne(['id' => $id]);
                if ($Project) {
                    if ( Yii::$app->user->identity->isInProject($Project->id)) {
                        $ret = true;
                    }
                }
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

}