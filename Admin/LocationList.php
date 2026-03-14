<?php

namespace Pickups\Admin;

use Pickups\Model\Model;
use Pickups\Model\Location;
use Pickups\Helpers\Data;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class LocationList extends \WP_List_Table {
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
            'city' => 'City',
            'description'  => 'Description'
        ];
    }

    public function prepare_items() {
       // echo "Preparing items...";
        try{
            $locations = Data::fetch_items(Location::class);
        }catch(\Exception $e){
            echo "Error fetching locations: " . $e->getMessage();
            return;
        }
        //$locations = Data::fetch_items(Location::class);
       $uploads = wp_upload_dir();


        $this->items = array_map(function($location) use ($uploads) {
            
            return [
                'ID'    => $location->getID(),
                'name'  => '<a href="'. esc_url( admin_url('admin.php?page=edit-location-form&ID=' . $location->getID()) ) . '">' . esc_html( $location->getName() ) . '</a>',
                'city' => $location->getCity(),
                'description' => $location->getDescription(),
                
            ];
        }, $locations);

        

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

        if($data['action'] === 'delete_selected_locations' && !empty($data['item'])) {
            $ids_to_delete = array_map('intval', $data['item']);
            foreach ($ids_to_delete as $id) {
                wp_delete_post($id, true);
            }
            echo '<div class="notice notice-success is-dismissible"><p>Selected locations deleted successfully!</p></div>';
        }
        //echo '<div class="wrap"><h1>Locations List</h1></div>';
        $controls =  <<<HTML
<form id="locations-filter-form" method="post">
<div class="">
    <script>
        function deleteSelected(event) {
            event.preventDefault();
            const action = document.querySelector('locations-filter-form input[name="action"]');
            action.value = 'delete_selected_locations';
            document.querySelector('locations-filter-form').submit();

        }

        
    </script>
    <input type="hidden" name="action" value="delete_selected_locations" />
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
            echo 'Error rendering location list: ' . $e->getMessage() . " trace " . $e->getTrace();
        }
  

    }
}