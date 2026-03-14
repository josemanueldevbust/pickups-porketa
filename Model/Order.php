<?php
namespace Pickups\Model;
class Order extends Model{
    
    const POST_TYPE = 'pickup_order';
    private $orderId;
    private $customer;
    private $products = [];
    private $totalAmount;

    private $orderDate;

    private $paymentStatus;

    private $paymentMethod;

    private $location;

    private $status;

    private $deliveryStatus;

    private $deliveryAddress;

    private $metadata;

    private $productCounts;

    public function setProductCounts($productCounts) {
        $this->productCounts = $productCounts;
    }

    public function getProductCounts() {
        return $this->productCounts;
    }

    public function getOrderId() {
        return $this->orderId;
    }
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }


    public function getCustomer() {
        return $this->customer;
    }
    public function setCustomer($customer) {
        $this->customer = $customer;
    }


    public function addProduct($product) {
        $this->products[] = $product;
        $this->totalAmount += $product->getPrice();
    }

    public function getProducts() {
        return $this->products;
    }
    public function setProducts($products) {
        $this->products = $products;
    }

    public function getTotalAmount() {
        return $this->totalAmount;
    }

    public function setMetadata($value) {
        $this->metadata = $value;
    }

    public function getMetadata() {
        return $this->metadata;
    }

    public function getOrderDate() {
        return $this->orderDate;
    }
    public function setOrderDate($orderDate) {
        $this->orderDate = $orderDate;
    }
    public function getPaymentStatus() {
        return $this->paymentStatus;
    }
    public function setPaymentStatus($paymentStatus) {
        $this->paymentStatus = $paymentStatus;
    }
    public function getDeliveryStatus() {
        return $this->deliveryStatus;
    }
    public function setDeliveryStatus($deliveryStatus) {
        $this->deliveryStatus = $deliveryStatus;
    }
    
    public function getDeliveryAddress() {
        return $this->deliveryAddress;
    }
    public function setDeliveryAddress($deliveryAddress) {
        $this->deliveryAddress = $deliveryAddress;
    }
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }
    public function getLocation() {
        return $this->location;
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    public function getStatus() {
        return $this->status;
    }
    public function setStatus($status) {
        $this->status = $status;
    }
    public function setTotalAmount($amount){
        $this->totalAmount = $amount;
    }   






    public static function process_order(\WP_REST_Request $data1){
        $data = json_decode($data1->get_body(), true);
      
        if(!empty($data)){
            $order = new Order(null);
            $order->setOrderDate($data['order_date']);
            $order->setPaymentStatus('Unconfirmed');
            $order->setDeliveryStatus('Pending');
            $order->setDeliveryAddress($data['address']);
            $order->setMetadata($data); 
            $customer = new Customer(null);
            $customer->setFirstName($data['first_name']);
            $customer->setLastName($data['last_name']);
            $customer->setEmail($data['email']);
            $customer->setPhoneNumber($data['phone']);
            $order->setCustomer($customer);
            $products = $data['products'];
            $productCounts = [];
            foreach($products as $product=>$quantity){
                $productCounts[$product] = $quantity;
                $productMod = \Pickups\Helpers\Data::fetch_item_by_id(Product::class, $product);
                $order->addProduct($productMod);
            }
            $order->setProductCounts($productCounts);

            $order->setLocation($data['location']);
            $order->setPaymentMethod($data['payment_method']);
            $order->setStatus('pending');
            $id = \Pickups\Helpers\Data::save_item($order);
            return [
                'success' => true,
                'message' => 'Su orden ha sido procesada con éxito',
                'id' => $id,
                'order' => $order
            ];
            

        }

        return [
            'success' => false,
            'message' => 'Error processing order'
        ];
    
    }
}