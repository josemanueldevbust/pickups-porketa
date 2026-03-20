<?php
require_once dirname(__FILE__) . '/../../../wp-load.php';

use Pickups\Model\Product;
use Pickups\Helpers\Data;

$jsonPath = __DIR__ . '/menu-data.json';
$jsonData = file_get_contents($jsonPath);
$data = json_decode($jsonData, true);

$menus = $data['menus'] ?? [];

$es = $menus['spanish'] ?? [];
$en = $menus['english'] ?? [];
$it = $menus['italian'] ?? [];

$categories = ['specialty', 'to_share', 'salads', 'sandwiches', 'burgers', 'desserts', 'wine_list'];

foreach ($categories as $cat) {
    if (!isset($es[$cat])) continue;
    
    // For 'specialty' it has an inner 'types' array
    $iter_es = ($cat === 'specialty') ? $es[$cat]['types'] : $es[$cat];
    $iter_en = ($cat === 'specialty') ? $en[$cat]['types'] : $en[$cat];
    $iter_it = ($cat === 'specialty') ? $it[$cat]['types'] : $it[$cat];

    foreach ($iter_es as $i => $item_es) {
        $item_en = $iter_en[$i] ?? [];
        $item_it = $iter_it[$i] ?? [];

        $product = new Product(null);
        $product->setCategory($cat);
        $product->setPrice($item_es['price'] ?? '');
        
        $product->setName_es($item_es['name'] ?? '');
        $product->setDesc_es($item_es['description'] ?? '');
        $product->setType_es($item_es['type'] ?? '');
        
        $product->setName_en($item_en['name'] ?? '');
        $product->setDesc_en($item_en['description'] ?? '');
        $product->setType_en($item_en['type'] ?? '');
        
        $product->setName_it($item_it['name'] ?? '');
        $product->setDesc_it($item_it['description'] ?? '');
        $product->setType_it($item_it['type'] ?? '');

        Data::save_item($product);
        echo "Imported " . $cat . ": " . ($item_es['name'] ?? 'Untitled') . "\n";
    }
}

// Drinks
if (isset($es['drinks'])) {
    $sub_cats = ['soft_drinks', 'coffee_and_tea', 'beers'];
    foreach ($sub_cats as $sub) {
        if (!isset($es['drinks'][$sub])) continue;
        
        $iter_es = $es['drinks'][$sub];
        $iter_en = $en['drinks'][$sub] ?? [];
        $iter_it = $it['drinks'][$sub] ?? [];

        foreach ($iter_es as $i => $item_es) {
            $item_en = $iter_en[$i] ?? [];
            $item_it = $iter_it[$i] ?? [];

            $product = new Product(null);
            $product->setCategory('drinks');
            $product->setSubcategory($sub);
            $product->setPrice($item_es['price'] ?? '');
            
            $product->setName_es($item_es['name'] ?? '');
            $product->setDesc_es($item_es['description'] ?? '');
            
            $product->setName_en($item_en['name'] ?? '');
            $product->setDesc_en($item_en['description'] ?? '');
            
            $product->setName_it($item_it['name'] ?? '');
            $product->setDesc_it($item_it['description'] ?? '');

            Data::save_item($product);
            echo "Imported drink " . $sub . ": " . ($item_es['name'] ?? 'Untitled') . "\n";
        }
    }
}

echo "Done!\n";
