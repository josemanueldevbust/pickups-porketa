<?php

/**
 * Plugin Name: Pickups Shortcodes
 * Description: A plugin for custom shortcodes used across the site for pick up plugin.
 * Version: 1.0
 * Author: Jose De Armas
 */


//require __DIR__ . "/vendor/autoload.php";

use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Logging\LoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\RequestLoggingConfigurationBuilder;
use PaypalServerSdkLib\Logging\ResponseLoggingConfigurationBuilder;
use Psr\Log\LogLevel;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\AmountBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\ItemBuilder;
use PaypalServerSdkLib\Models\ItemCategory;
use PaypalServerSdkLib\Models\Builders\ShippingDetailsBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingNameBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingOptionBuilder;
use PaypalServerSdkLib\Models\ShippingType;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\PaypalWalletBuilder;
use PaypalServerSdkLib\Models\Builders\PaypalWalletExperienceContextBuilder;
use PaypalServerSdkLib\Models\ShippingPreference;
use PaypalServerSdkLib\Models\PaypalExperienceLandingPage;
use PaypalServerSdkLib\Models\PaypalExperienceUserAction;
use PaypalServerSdkLib\Models\Builders\CallbackConfigurationBuilder;
use PaypalServerSdkLib\Models\Builders\PhoneNumberWithCountryCodeBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentSourceBuilder;
use PaypalServerSdkLib\Models\CallbackEvents;
use Pickups\Helpers\Data;
use Pickups\Model\Product;


$PAYPAL_CLIENT_ID = getenv("PAYPAL_CLIENT_ID");
$PAYPAL_CLIENT_SECRET = getenv("PAYPAL_CLIENT_SECRET");
spl_autoload_register(function ($class) {
    // Define your plugin's base namespace
    $prefix = 'Pickups\\';

    // Base directory for the namespace
    $base_dir = __DIR__ .'/'; // Adjust to your structure

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // Not our class
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});




function pick_up_button_shortcode() {
    return 'Hello from the Custom Plugin!';
}



\Pickups\Helpers\Config::configure('Product', 'product', 'Product');
\Pickups\Helpers\Config::configure('Location', 'location', 'Location');
\Pickups\Helpers\Config::configure('Order', 'order', 'Order', ['edit','add']);


add_shortcode( 'pick_upbutton', 'pick_up_button_shortcode' );

/**
 * Locations
 * @return void
 */



function render_pickup() {
    Pickups\User\OrderNow::render();
}

add_shortcode( 'render_pickup', 'render_pickup' );
add_action('rest_api_init', function () {
    register_rest_route('pickups/v1', '/payment/orders', array(
        'methods'  => 'POST',
        'callback' => 'process_orders',
        'permission_callback' => '__return_true'
    ));
});
add_action('rest_api_init', function () {
    register_rest_route('pickups/v1', '`/payment/orders/(?P<order_id>[a-zA-Z0-9_-]+)/capture`', array(
        'methods'  => 'POST',
        'callback' => 'capture_orders',
        'permission_callback' => '__return_true'
    ));
});


function handleResponse($response)
{
    $jsonResponse = json_decode($response->getBody(), true);
    return [
        "jsonResponse" => $jsonResponse,
        "httpStatusCode" => $response->getStatusCode(),
    ];
}
/**
 * Create an order to start the transaction.
 * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
 */
function createOrder($cart)
{
    global $client;

    $products = Data::fetch_items(Product::class);
   
    $total = 0;

    foreach($cart as $item){
        $prod = array_find($products, function($prod) use ($item){
            return $prod->getID() == $item["id"];
        });
        $total += $prod->getPrice() * $item["quantity"];
    }

    $orderBody = [
        "body" => OrderRequestBuilder::init("CAPTURE", [
            PurchaseUnitRequestBuilder::init(
                AmountWithBreakdownBuilder::init("USD", "100")
                    ->breakdown(
                        AmountBreakdownBuilder::init()
                            ->itemTotal(
                                MoneyBuilder::init("USD", "$total")->build()
                            )
                            ->build()
                    )
                    ->build()
            )
                // lookup item details in `cart` from database
                ->items(array_map(function($cartItem) use ($products){
                    $prod = array_find($products, function($prod) use ($cartItem){
                        return $prod->getID() == $cartItem["id"];
                    });
                    $amount = $prod->getPrice() * $cartItem["quantity"];
                    return ItemBuilder::init(
                        $prod->getName(),
                        MoneyBuilder::init("USD", "$amount")->build(),
                        $cartItem["quantity"])
                        ->description($prod->getDescription())
                        ->sku("sku" . $prod->getID())
                        ->category(ItemCategory::PHYSICAL_GOODS)
                        ->build();
                        },$cart))

                ->build(),
        ])
        ->build(),
    ];
   

    $apiResponse = $client->getOrdersController()->createOrder($orderBody);

    return handleResponse($apiResponse);
}
function captureOrder($orderID)
{
    global $client;

    $captureBody = [
        "id" => $orderID,
    ];

    $apiResponse = $client->getOrdersController()->captureOrder($captureBody);

    return handleResponse($apiResponse);
}
function capture_orders($request){
    global $wp;
    $current_path = $wp->request;

// Break it into segments
    $urlSegments = explode('/', $current_path);// Specific for JSON bodies
    end($urlSegments); // Will set the pointer to the end of array
    $orderID = prev($urlSegments);
    header("Content-Type: application/json");
    try {
        $captureResponse = captureOrder($orderID);
        echo json_encode($captureResponse["jsonResponse"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
        http_response_code(500);
    }
}
function process_orders($request) {
    $data = $request->get_json_params(); // Specific for JSON bodies
    
    $cart = $data["cart"];
    header("Content-Type: application/json");
    try {
        $orderResponse = createOrder($cart);
        echo json_encode($orderResponse["jsonResponse"]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
        http_response_code(500);
    }


    // return new WP_REST_Response(array(
    //     'success' => true,
    //     'message' => 'Data received for ' . $email
    // ), 200);
}

Pickups\Helpers\Asset::enqueue_all();
Pickups\Helpers\Data::create_rest('order',[
    'post'=>function($data){
        return Pickups\Model\Order::process_order($data);
    }   
])

?>