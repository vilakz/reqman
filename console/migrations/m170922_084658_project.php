<?php

use yii\db\Migration;

class m170922_084658_project extends Migration
{
    public function safeUp()
    {
        $strCurrentTimestamp = \common\components\DbEngineInfo::getCurrentTimestamp6($this->db);

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('project', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(1024)->notNull()->comment("Название проекта"),
            'createdAt' => "TIMESTAMP(6) NULL",
            'updatedAt' => "TIMESTAMP(6) DEFAULT $strCurrentTimestamp ON UPDATE $strCurrentTimestamp",
        ], $tableOptions);
        $this->addCommentOnTable('project', 'Проекты');
        $this->addCommentOnColumn('project', 'createdAt', 'Время создания');
        $this->addCommentOnColumn('project', 'updatedAt', 'Время изменения');
        $this->createIndex('updatedAtIndex', 'project', 'updatedAt');
    }

    public function safeDown()
    {
        $this->dropTable('project');
    }

}
