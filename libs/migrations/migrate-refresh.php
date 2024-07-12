<?php

if ($argc < 2) {
    echo "Usage: php migrate-refresh.php <table_name>\n";
    exit(1);
}

$tableName = $argv[1];

require 'migrate-reset.php';
require 'migrate.php';
