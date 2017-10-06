<?php

namespace common\controllers;
use common\models\Entity;
use Yii;
use yii\base\Controller;

/**
 * Общие Entity экшены для браузера и REST
 */
class EntityOverallController
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
                'matchCallback' => function($rule, $action) {
                    return static::isUserInIdEntity();
                },
            ],
            [
                'actions' => ['create'],
                'allow' => true,
                'roles' => ['projectEdit'],
            ],
            [
                'actions' => ['update', 'delete', 'select-path'],
                'allow' => true,
                'roles' => ['projectEdit'],
                'matchCallback' => function($rule, $action) {
                    return static::isUserInIdEntity();
                },
            ],
        ];
    }

    /**
     * Проверка что текущий пользователь состоит в проекте по сущности, где id сущности в HTTP запросе id
     * @return bool
     */
    public static function isUserInIdEntity()
    {
        $ret = false;
        if (!Yii::$app->user->can('administrator')) {
            // проверить что пользователь входит в проект
            $id = Yii::$app->request->get('id');
            if ($id) {
                $Entity = Entity::findOne(['id' => $id]);
                if ($Entity) {
                    $Project = $Entity->project;
                    if ($Project) {
                        if ( Yii::$app->user->identity->isInProject($Project->id)) {
                            $ret = true;
                        }
                    } else {
                        // Если сущность без проекта, то она разрешена несмотря на проект
                        $ret = true;
                    }
                }
            }
        } else {
            $ret = true;
        }
        return $ret;
    }

    /**
     * Получить возможные path по нескольким символам
     * @param $Controller Controller
     * @param $id integer Id Entity
     * @return array
     */
    public static function actionSelectPath($Controller, $id)
    {
        if ($Controller instanceof \yii\rest\Controller) {
            $bodyParams = Yii::$app->request->bodyParams;
            $word = (isset($bodyParams['word']) ? $bodyParams['word'] : '');
        } else {
            $word = Yii::$app->request->get('word', '');
        }
        $query = Entity::find()
            ->orderBy(['entity.path' => SORT_DESC])
            ->andWhere(new \yii\db\Expression("`entity`.`path` LIKE '%".(static::processSlashes($word))."%'") )            ;

        if (!\Yii::$app->user->can('administrator')) {
            $User = Yii::$app->user->identity;
            $query->joinWith(['project' => function($query) use ($User) {
                /** @var $query \yii\db\ActiveQuery */
                $query->joinWith(['userProjects' => function($query) use ($User) {
                }]);
            }]);
            $query->andWhere([
                'or',
                ['entity.projectId' => null],
                ['userProject.userId' => $User->id],
            ]);
        }
        $result = $query->asArray()->all();
        array_walk($result, function(&$item){
            // защита вывода, т.к. в result все данные из таблиц entity, связанные с ней project, userProject
            $new = [
                'id' => $item['id'],
                'path' => $item['path'],
            ];
            $item = $new;
        });
        return $result;
    }

    /**
     * Слешевые дела
     * @param $word
     * @return mixed|string
     */
    protected static function processSlashes($word)
    {
        $wordQuery = str_replace('\\', '\\\\\\\\', $word);
        if (0 === strpos($wordQuery, '\\')) {
            $strAfterReplace = preg_replace('/^(\\\\+)([^\x00]*?)$/ui','${2}',$wordQuery);
            if (0 == strlen($strAfterReplace)) {
                // если в поиске только один слеш, то выдать такую последовательность
                $wordQuery = '\\\\\\\\\\\\';
            } else {
                $wordQuery = '\\\\'.$strAfterReplace;
            }
        }
        return $wordQuery;
    }

}