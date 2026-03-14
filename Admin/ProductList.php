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
            'name' => 'Name',
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
                'name'  => '<a href="'. esc_url( admin_url('admin.php?page=edit-product-form&ID=' . $product->getID()) ) . '">' . esc_html( $product->getName() ) . '</a>',
                'Price' => $product->getPrice(),
                
                'image' => '<img src="' . $url . '" alt="' . $product->getName() . '" width="50" />',
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
        //echo '<div class="wrap"><h1>Products List</h1></div>';
        $controls =  <<<HTML
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
        }catch(\Exception $e){
            echo 'Error rendering product list: ' . $e->getMessage() . " trace " . $e->getTrace();
        }
  

    }
}