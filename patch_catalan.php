<?php
$menuPath = __DIR__ . '/menu-data.json';
$catPath = __DIR__ . '/catalan.json';

$menuData = json_decode(file_get_contents($menuPath), true);
$catData = json_decode(file_get_contents($catPath), true);

$menuData['menus']['catalan'] = $catData;

file_put_contents($menuPath, json_encode($menuData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Successfully integrated Catalan translation!\n";
