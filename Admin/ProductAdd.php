<?php
namespace Pickups\Admin;

use ElementorDeps\Twig\Cache\NullCache;
use Pickups\Model\Model;
use Pickups\Model\Product;
use Pickups\Helpers\Data;
class ProductAdd {
    // Class implementation goes here

    public static function render() {

        $url = '';
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (!empty($data)) {
            $product = new Product(null);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image'])) {
            $file = $_FILES['product_image'];

            // Example: move to uploads directory
            $uploads = wp_upload_dir();
            $target = $uploads['basedir'] . '/' . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $url  =  $uploads['baseurl'] . '/' . basename($file['name']);
                echo 'File uploaded to: ' . $uploads['baseurl'] . '/' . basename($file['name']);
            } else {
                echo 'Upload failed.';
            }
        }


            $product->setName($data['product_name']);
            $product->setPrice($data['product_price']);
            $product->setImage($url ?? '');
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
        //var_dump($locations);
        ?>
        <div class="wrap">
            <h1>Add New Product</h1>
            <form enctype="multipart/form-data" method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="product_name">Product Name</label></th>
                        <td><input type="text" id="product_name" name="product_name" required></td>
                    </tr>
                    <tr>
                        <th><label for="product_price">Price</label></th>
                        <td><input type="number" id="product_price" name="product_price" step="0.01" required></td>
                    </tr>
                    <tr>
                        <th><label for="product_image">Image URL</label></th>
                        <td><input type="file" id="product_image" name="product_image"></td>
                    </tr>
                    <tr>
                        <th><label for="product_description">Description</label></th>
                        <td><textarea id="product_description" name="product_description"></textarea></td>
                    </tr>
                    <tr>    
                        <th><label for="product_location">Location</label></th>
                        <td>
                            <select id="product_location" name="product_location">
                                <?php foreach($locations as $location): ?>
                                    <option value="" >
                                        Select
                                    </option>
                                    <option value="<?php echo esc_attr($location->getID()); ?>">
                                        <?php echo esc_html($location->getCity()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <button type="submit">Add Product</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php

    }

}