<?php

use yii\db\Migration;

class m171019_091249_user_options extends Migration
{
    public function safeUp()
    {
        $strCurrentTimestamp = \common\components\DbEngineInfo::getCurrentTimestamp6($this->db);

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('userOption', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer()->unsigned()->notNull()->comment('Пользователь'),
            'projectId' => $this->integer()->unsigned()->Null()->comment('Проект'),
            'name' => $this->string(30)->notNull()->comment('Название'),
            'value' => $this->text()->notNull()->comment('Значение'),
            'createdAt' => 'TIMESTAMP(6) NULL',
            'updatedAt' => "TIMESTAMP(6) DEFAULT $strCurrentTimestamp ON UPDATE $strCurrentTimestamp",
        ], $tableOptions);

        $this->addCommentOnTable('userOption', 'Опции для пользователей');
        $this->addCommentOnColumn('userOption', 'createdAt', 'Время создания');
        $this->addCommentOnColumn('userOption', 'updatedAt', 'Время изменения');

        $this->createIndex('updatedAtIndex', 'userOption', 'updatedAt');
        $this->createIndex('userIdIndex', 'userOption', 'userId');
        $this->createIndex('projectIdIndex', 'userOption', 'projectId');

        $this->addForeignKey('FKuserOptionUserIndex', 'userOption', 'userId', 'user', 'id', 'NO ACTION', 'CASCADE');
        $this->addForeignKey('FKuserOptionProjectIndex', 'userOption', 'projectId', 'project', 'id', 'NO ACTION', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('FKuserOptionUserIndex', 'userOption');
        $this->dropForeignKey('FKuserOptionProjectIndex', 'userOption');
        $this->dropTable('userOption');
    }
}
