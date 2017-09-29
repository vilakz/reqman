<?php

use yii\db\Migration;

class m170929_104801_entity_path extends Migration
{

    public function safeUp()
    {
        $this->addColumn('entity', 'path', $this->string(200)->null()->comment('Путь'));
    }

    public function safeDown()
    {
        $this->dropColumn('entity', 'path');
    }

}
