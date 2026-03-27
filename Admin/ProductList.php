<?php

namespace Pickups\Admin;

use Pickups\Model\Model;
use Pickups\Model\Product;
use Pickups\Helpers\Data;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class ProductList extends \WP_List_Table {
    private $manual_items;

    public function __construct($items = []) {
        $this->manual_items = $items;
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
            'Order' => 'Order',
            'image'  => 'Image',
        ];
    }

    public function prepare_items() {
        $products = $this->manual_items;
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
                'Order' => $product->getProduct_order() ?: 0,
                
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

        if (isset($_POST['action']) && $_POST['action'] === 'save_category_settings') {
            $cat_meta = $_POST['cat_meta'] ?? [];
            update_option('pickups_category_meta', $cat_meta);
            echo '<div class="notice notice-success is-dismissible"><p>Category settings saved successfully!</p></div>';
        }

        if (isset($_POST['action']) && $_POST['action'] === 'save_allergen_settings') {
            $allergen_meta = $_POST['allergen_meta'] ?? [];
            update_option('pickups_allergen_meta', $allergen_meta);
            echo '<div class="notice notice-success is-dismissible"><p>Allergen settings saved successfully!</p></div>';
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
                                $product->setAllergens($item_es['allergens'] ?? '');
                                $product->setProduct_order($item_es['product_order'] ?? 0);

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
                                    $product->setAllergens($item_es['allergens'] ?? '');
                                    $product->setProduct_order($item_es['product_order'] ?? 0);

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
                                    
                                    if (isset($item['allergens'])) {
                                        $product->setAllergens($item['allergens']);
                                    }
                                    
                                    if (isset($item['product_order'])) {
                                        $product->setProduct_order($item['product_order']);
                                    }
                                    
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

                                        if (isset($item['allergens'])) {
                                            $product->setAllergens($item['allergens']);
                                        }

                                        if (isset($item['product_order'])) {
                                            $product->setProduct_order($item['product_order']);
                                        }

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
        try {
            $all_products = Data::fetch_items(Product::class) ?: [];
            
            // Define categories and labels
            $categories = [
                'specialty' => 'Especialidades / Specialty',
                'to_share' => 'Para Compartir / To Share',
                'salads' => 'Ensaladas / Salads',
                'sandwiches' => 'Sandwiches',
                'burgers' => 'Burgers',
                'desserts' => 'Postres / Desserts',
                'drinks' => 'Bebidas / Drinks',
                'wine_list' => 'Vinos / Wine List'
            ];

            // Group products
            $grouped = [];
            foreach ($all_products as $p) {
                if (!$p) continue;
                $grouped[$p->getCategory()][] = $p;
            }

            echo $controls;

            // Category Management UI
            wp_enqueue_media();
            $cat_meta = get_option('pickups_category_meta', []);
            echo '<div class="wrap" style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<h3>Category Settings (Order & Images)</h3>';
            echo '<form method="post">';
            echo '<input type="hidden" name="action" value="save_category_settings" />';
            echo '<table class="widefat fixed" style="margin-bottom: 10px;">';
            echo '<thead><tr><th>Category</th><th>Order</th><th>Image URL</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            foreach ($categories as $catKey => $catLabel) {
                $order = $cat_meta[$catKey]['order'] ?? 0;
                $image = $cat_meta[$catKey]['image'] ?? '';
                $descriptions = $cat_meta[$catKey]['description'] ?? [];
                
                echo '<tr style="background: #f9f9f9;">';
                echo '<td><strong>' . esc_html($catLabel) . '</strong><br/><small>' . esc_html($catKey) . '</small></td>';
                echo '<td><input type="number" name="cat_meta[' . esc_attr($catKey) . '][order]" value="' . esc_attr($order) . '" style="width: 60px;" /></td>';
                echo '<td><input type="text" id="cat_image_' . esc_attr($catKey) . '" name="cat_meta[' . esc_attr($catKey) . '][image]" value="' . esc_attr($image) . '" class="regular-text" placeholder="https://..." /></td>';
                echo '<td><button type="button" class="button category-upload-button" data-target="cat_image_' . esc_attr($catKey) . '">Upload Image</button></td>';
                echo '</tr>';

                echo '<tr>';
                echo '<td colspan="4" style="padding: 10px 15px 20px 15px; border-bottom: 2px solid #eee;">';
                echo '<div style="display: flex; flex-wrap: wrap; gap: 10px;">';
                foreach (['spanish' => 'Spanish', 'english' => 'English', 'ca' => 'Catalan', 'italian' => 'Italian', 'french' => 'French'] as $lKey => $lName) {
                    $desc = $descriptions[$lKey] ?? '';
                    echo '<div style="flex: 1; min-width: 180px;">';
                    echo '<label style="display: block; font-size: 11px; font-weight: bold; margin-bottom: 2px;">' . $lName . ' Description</label>';
                    echo '<textarea name="cat_meta[' . esc_attr($catKey) . '][description][' . $lKey . ']" style="width: 100%;" rows="2">' . esc_textarea($desc) . '</textarea>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';

            echo "
            <script>
            jQuery(document).ready(function($){
                $('.category-upload-button').click(function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var targetId = button.data('target');
                    var custom_uploader = wp.media({
                        title: 'Select Category Image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    }).on('select', function() {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        $('#' + targetId).val(attachment.url);
                    }).open();
                });
            });
            </script>
            ";

            echo '<button type="submit" class="button button-primary">Save Category Settings</button>';
            echo '</form></div>';

            // Allergen Management UI
            $allergen_meta = get_option('pickups_allergen_meta', []);
            if (empty($allergen_meta)) {
                $allergen_defaults = [
                    'C' => [
                        'spanish' => 'Gluten',
                        'english' => 'Gluten',
                        'ca'      => 'Gluten',
                        'italian' => 'Glutine',
                        'french'  => 'Gluten'
                    ],
                    'O' => [
                        'spanish' => 'Soja',
                        'english' => 'Soy',
                        'ca'      => 'Soja',
                        'italian' => 'Soia',
                        'french'  => 'Soja'
                    ],
                    'M' => [
                        'spanish' => 'Leche',
                        'english' => 'Milk',
                        'ca'      => 'Llet',
                        'italian' => 'Latte',
                        'french'  => 'Lait'
                    ],
                    'E' => [
                        'spanish' => 'Mostaza',
                        'english' => 'Mustard',
                        'ca'      => 'Mostassa',
                        'italian' => 'Senape',
                        'french'  => 'Moutarde'
                    ],
                    'P' => [
                        'spanish' => 'Huevo',
                        'english' => 'Egg',
                        'ca'      => 'Ou',
                        'italian' => 'Uovo',
                        'french'  => 'Œuf'
                    ],
                    'R' => [
                        'spanish' => 'Frutos de cascara',
                        'english' => 'Nuts',
                        'ca'      => 'Fruit de closca',
                        'italian' => 'Frutta a guscio',
                        'french'  => 'Fruits à coque'
                    ],
                    'T' => [
                        'spanish' => 'Sesamo',
                        'english' => 'Sesame',
                        'ca'      => 'Sèsam',
                        'italian' => 'Sesamo',
                        'french'  => 'Sésame'
                    ]
                ];
                $allergen_meta = $allergen_defaults;
            }

            echo '<div class="wrap" style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<h3>Allergen Translations</h3>';
            echo '<form method="post">';
            echo '<input type="hidden" name="action" value="save_allergen_settings" />';
            echo '<table class="widefat fixed" style="margin-bottom: 10px;">';
            echo '<thead><tr><th style="width: 50px;">Code</th><th>Spanish</th><th>English</th><th>Italian</th><th>Catalan</th><th>French</th></tr></thead>';
            echo '<tbody>';
            foreach ($allergen_meta as $code => $langs) {
                echo '<tr>';
                echo '<td><strong>' . esc_html($code) . '</strong></td>';
                foreach (['spanish', 'english', 'italian', 'ca', 'french'] as $lang) {
                    $val = $langs[$lang] ?? '';
                    echo '<td><input type="text" name="allergen_meta[' . esc_attr($code) . '][' . esc_attr($lang) . ']" value="' . esc_attr($val) . '" style="width: 100%;" /></td>';
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '<button type="submit" class="button button-primary">Save Allergen Settings</button>';
            echo '</form></div>';

            // Sort categories by stored order
            uasort($categories, function($a_label, $b_label) use ($categories, $cat_meta) {
                $a_key = array_search($a_label, $categories);
                $b_key = array_search($b_label, $categories);
                $a_order = (int)($cat_meta[$a_key]['order'] ?? 0);
                $b_order = (int)($cat_meta[$b_key]['order'] ?? 0);
                return $a_order <=> $b_order;
            });

            foreach ($categories as $catKey => $catLabel) {
                if (empty($grouped[$catKey])) continue;

                // Sort by product_order
                usort($grouped[$catKey], function($a, $b) {
                    $orderA = (int)$a->getProduct_order();
                    $orderB = (int)$b->getProduct_order();
                    return $orderA <=> $orderB;
                });

                echo '<h2 style="margin-top: 40px; padding: 10px; background: #23282d; color: #fff; border-radius: 4px;">' . esc_html($catLabel) . '</h2>';
                
                $instance = new self($grouped[$catKey]);
                $instance->prepare_items(); 
                $instance->display();
            }

            echo '</form>';
            echo '</div>';
        } catch (\Exception $e) {
            echo 'Error rendering product list: ' . $e->getMessage();
        }
  

    }
}