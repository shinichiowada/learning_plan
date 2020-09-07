<?php

// DB接続の情報
define('DSN', 'mysql:host=db;dbname=learning_plan;charset=utf8');
define('DB_USER', 'admin_user');
define('DB_PASSWORD', '1234');

// エラー表示の設定(Noticeが表示されなくなる)
error_reporting(E_ALL & ~E_NOTICE);
