<?php

namespace Pickups\Admin;

use Pickups\Model\Model;
use Pickups\Model\Product;
use Pickups\Helpers\Data;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class ProductList extends \WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false,
        ]);
    }

    public function get_columns() {
        return [
            'cb'    => '<input type="checkbox" />',
            'name' => 'Name (ES)',
            'Category' => 'Category',
            'Location' => 'Location',
            'Price' => 'Price',
            'image'  => 'Image',
        ];
    }

    public function prepare_items() {
       // echo "Preparing items...";
        try{
            $products = Data::fetch_items(Product::class);
        }catch(\Exception $e){
            echo "Error fetching products: " . $e->getMessage();
            return;
        }
        //$products = Data::fetch_items(Product::class);
       $uploads = wp_upload_dir();

        $getLocation = function($item){
            $loc = $item->getLocation();
            if(!empty($loc)){  
                $locations = Data::fetch_items(\Pickups\Model\Location::class, []);
                $location = array_filter($locations, function($location) use ($loc) {
                    return $location->getID() == $loc;
                });
                if(!empty($location)){
                    return $location[0];
                }
            }
            return null;
        };
        $this->items = array_map(function($product) use ($uploads, $getLocation) {
            $url = $product->getImage();
            return [
                'ID'    => $product->getID(),
                'name'  => '<a href="'. esc_url( admin_url('admin.php?page=edit-product-form&ID=' . $product->getID()) ) . '">' . esc_html( $product->getName_es() ?: 'Untitled' ) . '</a>',
                'Category' => $product->getCategory() . ($product->getSubcategory() ? ' - ' . $product->getSubcategory() : ''),
                'Price' => $product->getPrice(),
                
                'image' => '<img src="' . $url . '" alt="' . $product->getName_es() . '" width="50" />',
                'Location' => $product->getLocation()
            ];
        }, $products);

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
    }

    public function column_cb($item) {

        return sprintf('<input type="checkbox" name="item[]" value="%s" />', $item['ID']);
    }

    public function column_default($item, $column_name) {
        return $item[$column_name] ?? '';
    }

    // Class implementation goes here

    public static function render() {
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if($data['action'] === 'delete_selected_products' && !empty($data['item'])) {
            $ids_to_delete = array_map('intval', $data['item']);
            foreach ($ids_to_delete as $id) {
                wp_delete_post($id, true);
            }
            echo '<div class="notice notice-success is-dismissible"><p>Selected products deleted successfully!</p></div>';
        }

        if (isset($_POST['action']) && $_POST['action'] === 'import_menu_data' && isset($_FILES['menu_json'])) {
            $file = $_FILES['menu_json'];
            $import_lang = $_POST['import_lang'] ?? 'all';

            if ($file['error'] === UPLOAD_ERR_OK) {
                $jsonData = file_get_contents($file['tmp_name']);
                $parsed = json_decode($jsonData, true);
                if ($parsed) {
                    $menus = $parsed['menus'] ?? $parsed; // fallback if wrapped

                    if ($import_lang === 'all') {
                        // Full multi-language format
                        $es = $menus['spanish'] ?? [];
                        $en = $menus['english'] ?? [];
                        $it = $menus['italian'] ?? [];

                        $categories = ['specialty', 'to_share', 'salads', 'sandwiches', 'burgers', 'desserts', 'wine_list'];

                        $count = 0;
                        foreach ($categories as $cat) {
                            if (!isset($es[$cat])) continue;
                            
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
                                $count++;
                            }
                        }

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
                                    $count++;
                                }
                            }
                        }
                        echo '<div class="notice notice-success is-dismissible"><p>Successfully imported ' . $count . ' new products!</p></div>';
                    } else {
                        // Single language format (update existing)
                        $existing_products = Data::fetch_items(Product::class) ?: [];
                        usort($existing_products, function($a, $b) { return $a->getID() <=> $b->getID(); });

                        $grouped = [];
                        foreach ($existing_products as $p) {
                            if (!$p) continue;
                            $grouped[$p->getCategory()][$p->getSubcategory()][] = $p;
                        }

                        $langData = $menus; // The root represents the single lang data
                        $categories = ['specialty', 'to_share', 'salads', 'sandwiches', 'burgers', 'desserts', 'wine_list'];
                        $count = 0;

                        foreach ($categories as $cat) {
                            if (!isset($langData[$cat])) continue;
                            $iter = ($cat === 'specialty') ? $langData[$cat]['types'] : $langData[$cat];
                            
                            foreach ($iter as $i => $item) {
                                if (isset($grouped[$cat][''][$i])) {
                                    $product = $grouped[$cat][''][$i];
                                    $nameMethod = 'setName_' . $import_lang;
                                    $descMethod = 'setDesc_' . $import_lang;
                                    $typeMethod = 'setType_' . $import_lang;

                                    if(method_exists($product, $nameMethod)) $product->$nameMethod($item['name'] ?? '');
                                    if(method_exists($product, $descMethod)) $product->$descMethod($item['description'] ?? '');
                                    if(method_exists($product, $typeMethod)) $product->$typeMethod($item['type'] ?? '');
                                    
                                    // Price and Image are kept as is to prevent overwriting base data
                                    Data::save_item($product);
                                    $count++;
                                }
                            }
                        }

                        if (isset($langData['drinks'])) {
                            $sub_cats = ['soft_drinks', 'coffee_and_tea', 'beers'];
                            foreach ($sub_cats as $sub) {
                                if (!isset($langData['drinks'][$sub])) continue;
                                $iter = $langData['drinks'][$sub];

                                foreach ($iter as $i => $item) {
                                    if (isset($grouped['drinks'][$sub][$i])) {
                                        $product = $grouped['drinks'][$sub][$i];
                                        $nameMethod = 'setName_' . $import_lang;
                                        $descMethod = 'setDesc_' . $import_lang;
                                        $typeMethod = 'setType_' . $import_lang;

                                        if(method_exists($product, $nameMethod)) $product->$nameMethod($item['name'] ?? '');
                                        if(method_exists($product, $descMethod)) $product->$descMethod($item['description'] ?? '');
                                        if(method_exists($product, $typeMethod)) $product->$typeMethod($item['type'] ?? '');

                                        Data::save_item($product);
                                        $count++;
                                    }
                                }
                            }
                        }
                        echo '<div class="notice notice-success is-dismissible"><p>Successfully updated matches for ' . $count . ' products in ' . strtoupper($import_lang) . '!</p></div>';
                    }
                }
            }
        }
        //echo '<div class="wrap"><h1>Products List</h1></div>';
        $controls =  <<<HTML
<div class="wrap">
    <hr class="wp-header-end">

    <form method="post" enctype="multipart/form-data" style="margin-top: 20px; margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); display: inline-block;">
        <h3 style="margin-top: 0;">Import Menu Data</h3>
        <p>Upload a JSON file. Choose the language context if importing translations sequentially over existing items.</p>
        <input type="hidden" name="action" value="import_menu_data" />
        <p>
            <label><strong>Language:</strong></label>
            <select name="import_lang">
                <option value="all">Multiple (Original menu-data.json payload)</option>
                <option value="ca">Catalan</option>
                <option value="fr">French</option>
                <option value="es">Spanish</option>
                <option value="en">English</option>
                <option value="it">Italian</option>
            </select>
        </p>
        <input type="file" name="menu_json" accept=".json,application/json" required />
        <button type="submit" class="button button-primary" onclick="return confirm('Are you sure you want to perform this import action?');">Import Products</button>
    </form>

<form id="products-filter-form" method="post">
<div class="">
    <script>
        function deleteSelected(event) {
            event.preventDefault();
            const action = document.querySelector('products-filter-form input[name="action"]');
            action.value = 'delete_selected_products';
            document.querySelector('products-filter-form').submit();

        }

        
    </script>
    <input type="hidden" name="action" value="delete_selected_products" />
    <button id="delete-selected" onclick="" class="button button-danger">Delete Selected</button>
</div>
HTML;
        try{
            $instance = new self();
            $instance->prepare_items(); 
            echo $controls;
            $instance->display();
            echo '</form>';
            echo '</div>';
        }catch(\Exception $e){
            echo 'Error rendering product list: ' . $e->getMessage() . " trace " . $e->getTrace();
        }
  

    }
}