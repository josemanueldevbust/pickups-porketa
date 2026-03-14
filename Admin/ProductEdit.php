<?php
namespace Pickups\Admin;

use ElementorDeps\Twig\Cache\NullCache;
use Pickups\Model\Model;
use Pickups\Model\Product;
use Pickups\Helpers\Data;
class ProductEdit {
    // Class implementation goes here

    public static function render() {

        $postId = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
        $product = Data::fetch_item_by_id(Product::class, $postId);


        $url = '';
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (!empty($data)) {
            //$product = new Product(null);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image'])) {
            $file = $_FILES['product_image'];

            // Example: move to uploads directory
            $uploads = wp_upload_dir();
            $target = $uploads['basedir'] . '/' . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $url  =  $uploads['baseurl'] . '/' . basename($file['name']);
                echo 'File uploaded to: ' . $uploads['baseurl'] . '/' . basename($file['name']);
            } else {
               // echo 'Upload failed.';
            }
        }


            $product->setName($data['product_name']);
            $product->setPrice($data['product_price']);
            if(!empty($file)){
                $product->setImage($url ?? '');
            }else{
            //$product->setImage($data['product_image']); 
            }
            
            $product->setDescription($data['product_description']);
            $product->setLocation($data['product_location']);

            try {
                Data::save_item($product);
                echo '<div class="notice notice-success is-dismissible"><p>Product added successfully!</p></div>';
            } catch (\Exception $e) {
                echo '<div class="notice notice-error is-dismissible"><p>Error adding product: ' . $e->getMessage() . '</p></div>';
            }
           
        }
        $locations = Data::fetch_items(\Pickups\Model\Location::class);
        ?>
        <div class="wrap">
            <h1>Edit Product</h1>
            <form enctype="multipart/form-data" method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="product_name">Product Name</label></th>
                        <td><input type="text" value="<?php echo $product->getName(); ?>" id="product_name" name="product_name" required></td>
                    </tr>
                    <tr>
                        <th><label for="product_price">Price</label></th>
                        <td><input type="number" value="<?php echo $product->getPrice(); ?>" id="product_price" name="product_price" step="0.01" required></td>
                    </tr>
                    <tr>
                        <th><label for="product_image">Image URL</label></th>
                       
                        <td>
                            <input type="file" id="product_image" value="<?php echo $product->getImage(); ?>" name="product_image">
                            <div>
                                <img style="max-width: 100%; max-height: 300px; object-fit: contain; object-position: left;" src="<?php echo $product->getImage(); ?>" />
                            </div>
                    
                        </td>
                    </tr>
                    <tr>
                        <th><label for="product_description">Description</label></th>
                        <td><textarea id="product_description" value="<?php echo $product->getDescription(); ?>" name="product_description">
                            <?php echo $product->getDescription(); ?>
                        </textarea></td>
                    </tr>
                    <tr>    
                        <th><label for="product_location">Location</label></th>
                        <td>
                            <select id="product_location" name="product_location" value="<?php echo $product->getLocation() ?>" >
                                 <option value="null" >
                                        Select
                                    </option>
                                <?php foreach($locations as $location): ?>
                                   
                                    <option <?php echo $product->getLocation() === $location->getID() ? 'selected' : '' ?> value="<?php echo esc_attr($location->getID()); ?>">
                                        <?php echo esc_html($location->getCity()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="submit">Save Product</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php

    }

}