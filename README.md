Управление требованиями
=======================

Простейшее приложение на Yii2.

Установка
=========

    composer global require "fxp/composer-asset-plugin:^1.3.1"
    git clone проект
    composer install
    В файле /environments/dev/common/config/main-local.php прописываются доступы к БД, а также альясы на frontend и backend доменов.
    ./init --full
    php yii migrate
    Для создания тестовых пользователей php yii misc/create-users (для них пароли 111111)