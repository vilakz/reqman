<?php
/* @var $User common\models\User */
/* @var $Project common\models\Project */
/* @var $password string */

?>

Вы были добавлены в проект <?= $Project->name ?>
Адрес <?= Yii::getAlias("@frontendWeb") ?>
Логин <?= $User->email ?>
<?php if (true !== $password) {?>
    Пароль <?= $password ?>
<?php } ?>

