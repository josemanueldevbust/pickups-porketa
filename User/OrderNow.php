<?php
namespace Pickups\User; 

use Pickups\Helpers\Data;
use Pickups\Model\Location;
use Pickups\Model\Product;
use Pickups\Model\Order;


class OrderNow {
    // Class implementation goes here

    private static function getChooserTemplates(){

        $locations =Data::fetch_items(Location::class);
        $products = Data::fetch_items(Product::class);

        $tplPath = __DIR__ . "/product_chooser.html";
        $tplHTML = file_get_contents($tplPath);
        $locationChoosers = [];
        foreach($locations as $location){
            $prods = [];
            foreach($products as $product){
                 $prods[] = [
                            'id' => $product->getID(),
                            'name' => $product->getName(),
                            'description' => $product->getDescription(),
                            'price' => $product->getPrice(),
                            'image' =>esc_url($product->getImage())
                        ];
            }
            $prodsJson = json_encode($prods);


            $locationChoosers[$location->getID()] = str_replace('[PRODUCTSJSON]', $prodsJson, $tplHTML);
        }

        return $locationChoosers;
        


    }
    public static function render() {

        $parts = [
            function() { self::location_form(); },
            function() { self::product_chooser_list(); },
            function() { self::customer_form(); },
            function() { self::payment_form(); },
            function() { self::order_summary(); },
            function() { self::order_confirmation(); }
        ];
        
        ?>

        <script>
            window.locationTemplates = <?= json_encode(self::getChooserTemplates()) ?>;
            window.ordersEndpoint = '<? rest_url('pickups/v1/orders'); ?>';
            window.ordersCaptureEndpoint = '<? rest_url('pickups/v1/orders'); ?>/[ORDERID]/capture';

         
        </script>
        <button style="    color: black;  background: white;    padding: .25rem;    margin: 1rem;    min-width: 7rem;    border-radius: 31px;" onclick="openOrderNow(event)">Pedir</button>
        <div class="order-now-container">

            <div id="order-now-container">
                <?= self::location_form() ?>
            </div>
            <iframe id="order-iframe" style="display: none">


            </iframe>


        </div>
        
       

        <?php
        
    }

    private static function close(){
         ?>
        <button class="close-btn" onclick="closeOrderNow(event)">Cancelar</button>
        <?php
    }
    private static function next_step() {
        ?>
        <button class="next-btn" onclick="nextStep(event)">Siguente</button>
        <?php
    }
    private static function location_form() {


        $locations =Data::fetch_items(Location::class);
        $locationData = array_map(function($location){
            return [
                'id' => $location->getID(),
                'name' => $location->getName(),
                'city' => $location->getCity()
            ];
        }, $locations);
        
        ?>
        <div class="loc-img">
        <svg fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.114-0.011c-6.559 0-12.114 5.587-12.114 12.204 0 6.93 6.439 14.017 10.77 18.998 0.017 0.020 0.717 0.797 1.579 0.797h0.076c0.863 0 1.558-0.777 1.575-0.797 4.064-4.672 10-12.377 10-18.998 0-6.618-4.333-12.204-11.886-12.204zM16.515 29.849c-0.035 0.035-0.086 0.074-0.131 0.107-0.046-0.032-0.096-0.072-0.133-0.107l-0.523-0.602c-4.106-4.71-9.729-11.161-9.729-17.055 0-5.532 4.632-10.205 10.114-10.205 6.829 0 9.886 5.125 9.886 10.205 0 4.474-3.192 10.416-9.485 17.657zM16.035 6.044c-3.313 0-6 2.686-6 6s2.687 6 6 6 6-2.687 6-6-2.686-6-6-6zM16.035 16.044c-2.206 0-4.046-1.838-4.046-4.044s1.794-4 4-4c2.207 0 4 1.794 4 4 0.001 2.206-1.747 4.044-3.954 4.044z"></path>
        </svg>
        </div>
        
        <h2>Seleccione su ubicación</h2>
        <ul class="pickup-location">
            <?php foreach($locations as $location): ?>
                <li>
                    <button class="loc-btn" onclick="setLocation(event, <?php echo $location->getID(); ?>)">
                        <?php echo esc_html($location->getName()); ?> - <?php echo esc_html($location->getCity()); ?>
                        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 12H18M18 12L13 7M18 12L13 17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <!-- <input onchange="setValue('location',event.target.value)" type="radio" id="location-<?php echo $location->getID(); ?>" name="pickup_location" value="<?php echo $location->getID(); ?>">
                    <label for="location-<?php echo $location->getID(); ?>"><?php echo esc_html($location->getName()); ?> - <?php echo esc_html($location->getCity()); ?></label> -->
                </li>
            <?php endforeach; ?>
        </ul>
        <script>
            var locations = <?php echo json_encode($locationData); ?>;
        </script>


        <?php
    }

    private static function product_chooser_list() {

        $products =Data::fetch_items(Product::class);
        $prodItems = [];
        ?>
       
        <div>
            <h2>Productos disponibles</h2>
            <ul class="product-list">
                <?php foreach($products as $product): ?>
                    <li>
                        <?php
                        $prodItems[] = [
                            'productId' => $product->getID(),
                            'name' => $product->getName(),
                            'price' => $product->getPrice(),
                            'image' => $product->getImage()
                        ];
                        ?>
                        <div class="product-image">
                            <img src="<?php echo esc_url($product->getImage()); ?>" alt="<?php echo esc_html($product->getName()); ?>" width="50" />
                        </div>

                        
                        <label for="product-<?php echo $product->getID(); ?>"><?php echo esc_html($product->getName()); ?> - $<?php echo esc_html($product->getPrice()); ?> <button onclick="addToOrder(event, <?php echo $product->getID(); ?>)">+</button></label>
                        <p style="text-align: center;"><?php echo esc_html($product->getDescription()); ?></p>

                        
                    </li>
                <?php endforeach; ?>
            </ul>
            <script>
                var products = <?php echo json_encode($prodItems); ?>;

            </script>
            <!-- Product listing would go here -->
        </div>
        <?php
    }


    private static function customer_form() {
        ?>
        <h2>Información de cliente</h2>
        <form method="post" action="">
            <label for="first_name">Primer nombre:</label>
            <input onchange="setValue('first_name', event.target.value)" type="text" id="first_name" name="first_name" required><br>

            <label for="last_name">Apellido:</label>
            <input  onchange="setValue('last_name', event.target.value)" type="text" id="last_name" name="last_name" required><br>

            <label for="email">Email:</label>
            <input  onchange="setValue('email', event.target.value)" type="email" id="email" name="email" required><br>

            <label for="phone">Teléfono:</label>
            <input  onchange="setValue('phone', event.target.value)" type="tel" id="phone" name="phone" required><br>

            <label for="address">Dirección:</label>
            <input  onchange="setValue('address', event.target.value)"type="text" id="address" name="address" required><br>


            
        </form>

<?php
    }


    private static function order_summary() {

        $url =  rest_url('pickups/v1/order');
;
        ?>
        
        <div>
            <h2>Resumen de su orden</h2>
            <!-- Order summary details would go here -->
             <ul class="order-summary-list">
                <!-- Dynamically populated order summary -->
            </ul>
            <button class="process" onclick="processOrder(event, '<?php echo $url ?>')">Procesar orden</button>
        </div>
        <?php
    }

    private static function payment_form() {
        ?>
        
            <h2>Información de pago</h2>
            <!-- Payment fields would go here -->
             <select name="payment_method" onchange="setValue('payment_method', event.target.value)" required>
                <option value="">Seleccione un método de pago</option>
                <option value="credit_card">TDD/TDC</option>
                <option value="paypal">PayPal</option>
            </select>
           
        <?php
    }

    private static function order_confirmation() {
        ?>

        <div>
            <h2>Confirmación de su orden</h2>
            <!-- Confirmation message would go here -->
             <p>Gracias por su orden</p>
             <p>Mantengase atento para recibir su pedido.</p>
             
        </div>
        <?php
    }

}