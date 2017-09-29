<?php

use yii\db\Migration;

class m170922_064024_rbac_data extends Migration
{

    public function safeUp()
    {
        $auth = \Yii::$app->authManager;

        $permProjectView = $auth->createPermission('projectView');
        $permProjectView->description = 'Просмотр проекта';
        $auth->add($permProjectView);

        $permProjectEdit = $auth->createPermission('projectEdit');
        $permProjectEdit->description = 'Участие в проекте';
        $auth->add($permProjectEdit);

        $permProjectAdmin = $auth->createPermission('projectAdmin');
        $permProjectAdmin->description = 'Администрирование проекта';
        $auth->add($permProjectAdmin);

        $permAdmin = $auth->createPermission('admin');
        $permAdmin->description = 'Администрирование сайта';
        $auth->add($permAdmin);

        $projectViewer = $auth->createRole('projectViewer');
        $auth->add($projectViewer);
        $auth->addChild($projectViewer, $permProjectView);


        $projectEditor = $auth->createRole('projectEditor');
        $auth->add($projectEditor);
        $auth->addChild($projectEditor, $projectViewer);
        $auth->addChild($projectEditor, $permProjectEdit);

        $projectAdministrator = $auth->createRole('projectAdministrator');
        $auth->add($projectAdministrator);
        $auth->addChild($projectAdministrator, $projectEditor);
        $auth->addChild($projectAdministrator, $permProjectAdmin);

        $admin = $auth->createRole('administrator');
        $auth->add($admin);
        $auth->addChild($admin, $projectAdministrator);
        $auth->addChild($admin, $permAdmin);

    }

    public function safeDown()
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();
    }

}
