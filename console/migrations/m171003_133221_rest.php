<?php

use yii\db\Migration;

class m171003_133221_rest extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'restToken', $this->string(50)->null()->comment("Токен доступа для REST"));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'restToken');
    }

}
