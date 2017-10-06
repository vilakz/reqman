<?php

namespace common\controllers;
use common\models\Requirement;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Общие Requirement экшены для браузера и REST
 */
class RequirementOverallController
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
                    return static::isUserInIdRequirement();
                },
            ],
            [
                'actions' => ['create'],
                'allow' => true,
                'roles' => ['projectEdit'],
            ],
            [
                'actions' => ['update', 'delete'],
                'allow' => true,
                'roles' => ['projectEdit'],
                'matchCallback' => function ($rule, $action) {
                    return static::isUserInIdRequirement();
                },
            ],
        ];
    }

    /**
     * Найти модель по Id
     * @param $id integer Id проекта
     * @return Requirement
     * @throws NotFoundHttpException
     */
    public static function findModel($id)
    {
        if (($model = Requirement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Проверка что текущий пользователь состоит в проекте по требованию, где id требования в HTTP запросе id
     * @return bool
     */
    public static function isUserInIdRequirement()
    {
        $ret = false;
        if (!Yii::$app->user->can('administrator')) {
            // проверить что пользователь входит в проект
            $id = Yii::$app->request->get('id');
            if ($id) {
                $Requirement = Requirement::findOne(['id' => $id]);
                if ($Requirement) {
                    $Project = $Requirement->project;
                    if ($Project) {
                        if ( Yii::$app->user->identity->isInProject($Project->id)) {
                            $ret = true;
                        }
                    }
                }
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

}