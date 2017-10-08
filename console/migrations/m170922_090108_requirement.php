<?php

use yii\db\Migration;

class m170922_090108_requirement extends Migration
{

    public function safeUp()
    {
        $strCurrentTimestamp = \common\components\DbEngineInfo::getCurrentTimestamp6($this->db);

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('requirement', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(200)->notNull()->comment("Требование"),
            'projectId' => $this->integer()->unsigned()->notNull()->comment("Проект"),
            'body' => $this->text()->null()->comment("Подробности требования"),
            'createdAt' => "TIMESTAMP(6) NULL",
            'updatedAt' => "TIMESTAMP(6) DEFAULT $strCurrentTimestamp ON UPDATE $strCurrentTimestamp",
        ], $tableOptions);
        $this->addCommentOnTable('requirement', 'Требования');
        $this->addCommentOnColumn('requirement', 'createdAt', 'Время создания');
        $this->addCommentOnColumn('requirement', 'updatedAt', 'Время изменения');
        $this->createIndex('updatedAtIndex', 'requirement', 'updatedAt');

        $this->createIndex('projectIdRequirementIndex', 'requirement', 'projectId');
        $this->addForeignKey('FKRequirementProjectIndex', 'requirement', 'projectId', 'project', 'id', 'NO ACTION', 'CASCADE');

        $this->createIndex('nameProjectIdRequirementIndex', 'requirement', ['name', 'projectId'], true);
    }

    public function safeDown()
    {
        $this->dropForeignKey('FKRequirementProjectIndex', 'requirement');
        $this->dropTable('requirement');
    }

}
