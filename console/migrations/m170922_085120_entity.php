<?php

use yii\db\Migration;

class m170922_085120_entity extends Migration
{

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('entity', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(255)->notNull()->comment("Название сущности"),
            'projectId' => $this->integer()->unsigned()->null()->comment("Проект"),
            'description' => $this->text()->null()->comment("Описание"),
            'createdAt' => 'TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6)',
            'updatedAt' => 'TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)',
        ], $tableOptions);
        $this->addCommentOnTable('entity', 'Сущности');
        $this->addCommentOnColumn('entity', 'createdAt', 'Время создания');
        $this->addCommentOnColumn('entity', 'updatedAt', 'Время изменения');
        $this->createIndex('updatedAtIndex', 'entity', 'updatedAt');

        $this->createIndex('projectIdEntityIndex', 'entity', 'projectId');
        $this->addForeignKey('FKEntityProjectIndex', 'entity', 'projectId', 'project', 'id', 'NO ACTION', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('FKEntityProjectIndex', 'entity');
        $this->dropTable('entity');
    }

}
