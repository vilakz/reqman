<?php
namespace api\controllers;

use api\models\ProjectSearch;
use common\controllers\ProjectOverallController;
use common\models\AddUser;
use common\models\Project;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * REST для Project
 */
class ProjectController extends ActiveController
{

    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\Project';

    /**
     * @inheritdoc
     */
    public $createScenario = Project::SCENARIO_REST_CREATE;

    /**
     * @inheritdoc
     */
    public $updateScenario = Project::SCENARIO_REST_UPDATE;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::className(),
            HttpBearerAuth::className(),
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => ProjectOverallController::getAccessRules(),
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return mixed
     */
    public function prepareDataProvider()
    {
        $searchModel = new ProjectSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * Добавить пользователя в проект
     * @param $id Project.id
     * @return array
     */
    public function actionAdd($id)
    {
        /** @var integer $id */
        $result = ProjectOverallController::actionAdd($this, $id);
        /** @var $AddUser AddUser */
        $AddUser = $result['addUser'];
        if ($AddUser->hasErrors()) {
            $ret = $AddUser;
        } else {
            $ret = [
                'projectId' => $result['project']->id,
                'result' => $result['result'],
                'message' => $AddUser->addUserMessage,
            ];
        }
        return $ret;
    }

    /**
     * Удалить пользователя из проекта
     * @param $id
     * @return array
     */
    public function actionUnsetUser($id)
    {
        return ProjectOverallController::actionUnsetUser($this, $id);
    }

}