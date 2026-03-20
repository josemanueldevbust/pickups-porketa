<?php
$menuPath = __DIR__ . '/menu-data.json';
$frPath = __DIR__ . '/french.json';

$menuData = json_decode(file_get_contents($menuPath), true);
$frData = json_decode(file_get_contents($frPath), true);

$menuData['menus']['french'] = $frData['menus'];

file_put_contents($menuPath, json_encode($menuData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully integrated French translation into menu-data.json!\n";
