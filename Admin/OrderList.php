<?php

namespace Pickups\Admin;

use Pickups\Model\Model;
use Pickups\Model\Order;
use Pickups\Helpers\Data;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}


class OrderList extends \WP_List_Table {
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
            'Date' => 'Fecha',
            'DeliveryStatus' => 'Estado de entrega',
            'PaymentStatus' => 'Estado de pago',
            'Address' => 'Dirección',
            'Products' => 'Productos',
            'TotalAmount' => 'Total',
            'CustomerName' => 'Cliente',
        ];
    }

    public function prepare_items() {
       // echo "Preparing items...";
        try{
            $orders = Data::fetch_items(Order::class);
        }catch(\Exception $e){
            echo "Error fetching orders: " . $e->getMessage();
            return;
        }
        //$orders = Data::fetch_items(Order::class);
       $uploads = wp_upload_dir();
        $custName = function($item){
            $cust = $item->getCustomer();
            if(!empty($cust)){  

                return $cust->getFirstName() . ' ' . $cust->getLastName();
            }
            return '';
        };

        $this->items = array_map(function($order) use ($uploads, $custName) {
            
            $products = $order->getProducts();
            $counts = $order->getProductCounts();
            
            return [
                'ID'    => $order->getID(),
                'Date' => $order->getOrderDate(),
                'DeliveryStatus' => $order->getDeliveryStatus(),
                'PaymentStatus' => $order->getPaymentStatus(),
                'Address' => $order->getDeliveryAddress(),
                'Products' => implode('<br>',array_map(function($product) use ($counts){
                    return $product->getName() . " x " . $counts[$product->getID()];
                }, $products)),
                'TotalAmount'=> $order->getTotalAmount(),
                'CustomerName'  => $custName($order)
                
                
            ];
        }, $orders);

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

        if($data['action'] === 'delete_selected_orders' && !empty($data['item'])) {
            $ids_to_delete = array_map('intval', $data['item']);
            foreach ($ids_to_delete as $id) {
                wp_delete_post($id, true);
            }
            echo '<div class="notice notice-success is-dismissible"><p>Selected orders deleted successfully!</p></div>';
        }
        //echo '<div class="wrap"><h1>orders List</h1></div>';
        $controls =  <<<HTML
<form id="orders-filter-form" method="post">
<div class="">
    <script>
        function deleteSelected(event) {
            event.preventDefault();
            const action = document.querySelector('orders-filter-form input[name="action"]');
            action.value = 'delete_selected_orders';
            document.querySelector('orders-filter-form').submit();

        }

        
    </script>
    <input type="hidden" name="action" value="delete_selected_orders" />
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
            echo 'Error rendering order list: ' . $e->getMessage() . " trace " . $e->getTrace();
        }
  

    }
}