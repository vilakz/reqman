<?php
/* @var $this yii\web\View */
/* @var $User common\models\User */
/* @var $Project common\models\Project */
/* @var $password string */

?>
<div>
    <p>Вы были добавлены в проект <?= $Project->name ?></p>
    <p>Адрес <?= Yii::getAlias("@frontendWeb") ?></p>
    <p>Логин <?= $User->email ?></p>
    <?php if (true !== $password) {?>
    <p>Пароль <?= $password ?></p>
    <?php } ?>
</div>
