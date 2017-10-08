<?php
namespace common\components\behaviors;

use common\components\DbEngineInfo;
use yii\db\Expression;

/**
 * Работа с timestamp(6) для MariaDB >= 5.5 и MySQL
 * Разные условия.
 * MariaDB :
 * `updatedAt` TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
 * нельзя создать более 1 поля с DEFAULT CURRENT_TIMESTAMP или ON UPDATE CURRENT_TIMESTAMP
 * MySQL :
 * `updatedAt` TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
 *
 * Также вынесен в отдельный класс, т.к. сериализация ругается на анонимные функции.
 * Сериализация для работы с кешем.
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{

    /**
     * @inheritdoc
     */
    public $createdAtAttribute = 'createdAt';

    /**
     * @inheritdoc
     */
    public $updatedAtAttribute = false;

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        return new Expression(DbEngineInfo::getCurrentTimestamp6($this->owner->db));
    }
}