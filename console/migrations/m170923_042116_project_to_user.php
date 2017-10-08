<?php

use yii\db\Migration;

class m170923_042116_project_to_user extends Migration
{

    public function safeUp()
    {
        $strCurrentTimestamp = \common\components\DbEngineInfo::getCurrentTimestamp6($this->db);

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('userProject', [
            'id' => $this->primaryKey()->unsigned(),
            'projectId' => $this->integer()->unsigned()->notNull()->comment("Проект"),
            'userId' => $this->integer()->unsigned()->notNull()->comment("Пользователь"),
            'updatedAt' => "TIMESTAMP(6) DEFAULT $strCurrentTimestamp ON UPDATE $strCurrentTimestamp",
        ], $tableOptions);
        $this->addCommentOnTable('userProject', 'Связь пользователя и проекта');
        $this->addCommentOnColumn('userProject', 'updatedAt', 'Время изменения');
        $this->createIndex('updatedAtIndex', 'userProject', 'updatedAt');
        $this->createIndex('projectIdUserIdIndex', 'userProject', ['projectId', 'userId'], true);

        $this->addForeignKey('FKUserProjectProjectIndex', 'userProject', 'projectId', 'project', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('FKUserProjectUserIndex', 'userProject', 'userId', 'user', 'id', 'NO ACTION', 'CASCADE');

    }

    public function safeDown()
    {
        $this->dropForeignKey('FKUserProjectProjectIndex', 'userProject');
        $this->dropForeignKey('FKUserProjectUserIndex', 'userProject');
        $this->dropTable('userProject');
    }

}
