<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string()->notNull()->unique(),
            'authKey' => $this->string(32)->notNull(),
            'passwordHash' => $this->string()->notNull(),
            'passwordResetToken' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'createdAt' => 'TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6)',
            'updatedAt' => 'TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)',
        ], $tableOptions);

        $this->addCommentOnColumn('{{%user}}', 'createdAt', 'Время создания');
        $this->addCommentOnColumn('{{%user}}', 'updatedAt', 'Время изменения');
        $this->createIndex('updatedAtIndex', '{{%user}}', 'updatedAt');
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
