<?php
namespace Pickups\Admin;

use ElementorDeps\Twig\Cache\NullCache;
use Pickups\Model\Model;
use Pickups\Model\Product;
use Pickups\Helpers\Data;

class ProductAdd {
    public static function render() {
        $url = '';
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (!empty($data)) {
            $product = new Product(null);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_image']) && !empty($_FILES['product_image']['name'])) {
                $file = $_FILES['product_image'];
                $uploads = wp_upload_dir();
                $target = $uploads['basedir'] . '/' . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $url  =  $uploads['baseurl'] . '/' . basename($file['name']);
                }
            }

            $product->setCategory($data['product_category']);
            $product->setSubcategory($data['product_subcategory'] ?? '');
            $product->setPrice($data['product_price']);
            
            $product->setName_es($data['name_es'] ?? '');
            $product->setDesc_es($data['desc_es'] ?? '');
            $product->setType_es($data['type_es'] ?? '');
            
            $product->setName_en($data['name_en'] ?? '');
            $product->setDesc_en($data['desc_en'] ?? '');
            $product->setType_en($data['type_en'] ?? '');
            
            $product->setName_it($data['name_it'] ?? '');
            $product->setDesc_it($data['desc_it'] ?? '');
            $product->setType_it($data['type_it'] ?? '');

            $product->setName_ca($data['name_ca'] ?? '');
            $product->setDesc_ca($data['desc_ca'] ?? '');
            $product->setType_ca($data['type_ca'] ?? '');

            $product->setName_fr($data['name_fr'] ?? '');
            $product->setDesc_fr($data['desc_fr'] ?? '');
            $product->setType_fr($data['type_fr'] ?? '');

            $product->setImage($url ?? '');
            $product->setLocation($data['product_location'] ?? '');

            try {
                Data::save_item($product);
                echo '<div class="notice notice-success is-dismissible"><p>Product added successfully!</p></div>';
            } catch (\Exception $e) {
                echo '<div class="notice notice-error is-dismissible"><p>Error adding product: ' . $e->getMessage() . '</p></div>';
            }
        }

        $locations = Data::fetch_items(\Pickups\Model\Location::class);
        $categories = [
            'specialty' => 'Especialidades',
            'salads' => 'Ensaladas',
            'to_share' => 'Para Compartir',
            'sandwiches' => 'Sandwiches',
            'burgers' => 'Hamburguesas',
            'desserts' => 'Postres',
            'drinks' => 'Bebidas',
            'wine_list' => 'Vinos'
        ];
        
        $subcategories = [
            'soft_drinks' => 'Refrescos',
            'coffee_and_tea' => 'Café y Té',
            'beers' => 'Cervezas'
        ];
        ?>
        <div class="wrap">
            <h1>Add New Product</h1>
            <form enctype="multipart/form-data" method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="product_category">Category</label></th>
                        <td>
                            <select id="product_category" name="product_category" required onchange="toggleSubcategory(this.value)">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $k => $v): ?>
                                    <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="row_subcategory" style="display:none;">
                        <th><label for="product_subcategory">Drink Subcategory</label></th>
                        <td>
                            <select id="product_subcategory" name="product_subcategory">
                                <option value="">Select Subcategory</option>
                                <?php foreach($subcategories as $k => $v): ?>
                                    <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <script>
                        function toggleSubcategory(val) {
                            document.getElementById('row_subcategory').style.display = (val === 'drinks') ? 'table-row' : 'none';
                        }
                    </script>

                    <tr>
                        <th><label for="product_price">Price</label></th>
                        <td><input type="text" id="product_price" name="product_price" placeholder="e.g. 13.99€" required></td>
                    </tr>
                    
                    <!-- ES -->
                    <tr><td colspan="2"><hr><h3 style="margin:0">Spanish</h3></td></tr>
                    <tr><th><label for="name_es">Name (ES)</label></th><td><input type="text" id="name_es" name="name_es" required></td></tr>
                    <tr><th><label for="desc_es">Description (ES)</label></th><td><textarea id="desc_es" name="desc_es"></textarea></td></tr>
                    <tr><th><label for="type_es">Type / Subtext (ES)</label></th><td><input type="text" id="type_es" name="type_es" placeholder="For wines"></td></tr>
                    
                    <!-- EN -->
                    <tr><td colspan="2"><hr><h3 style="margin:0">English</h3></td></tr>
                    <tr><th><label for="name_en">Name (EN)</label></th><td><input type="text" id="name_en" name="name_en"></td></tr>
                    <tr><th><label for="desc_en">Description (EN)</label></th><td><textarea id="desc_en" name="desc_en"></textarea></td></tr>
                    <tr><th><label for="type_en">Type / Subtext (EN)</label></th><td><input type="text" id="type_en" name="type_en" placeholder="For wines"></td></tr>
                    
                    <!-- IT -->
                    <tr><td colspan="2"><hr><h3 style="margin:0">Italian</h3></td></tr>
                    <tr><th><label for="name_it">Name (IT)</label></th><td><input type="text" id="name_it" name="name_it"></td></tr>
                    <tr><th><label for="desc_it">Description (IT)</label></th><td><textarea id="desc_it" name="desc_it"></textarea></td></tr>
                    <tr><th><label for="type_it">Type / Subtext (IT)</label></th><td><input type="text" id="type_it" name="type_it" placeholder="For wines"></td></tr>

                    <!-- CA -->
                    <tr><td colspan="2"><hr><h3 style="margin:0">Catalan</h3></td></tr>
                    <tr><th><label for="name_ca">Name (CA)</label></th><td><input type="text" id="name_ca" name="name_ca"></td></tr>
                    <tr><th><label for="desc_ca">Description (CA)</label></th><td><textarea id="desc_ca" name="desc_ca"></textarea></td></tr>
                    <tr><th><label for="type_ca">Type / Subtext (CA)</label></th><td><input type="text" id="type_ca" name="type_ca" placeholder="For wines"></td></tr>

                    <!-- FR -->
                    <tr><td colspan="2"><hr><h3 style="margin:0">French</h3></td></tr>
                    <tr><th><label for="name_fr">Name (FR)</label></th><td><input type="text" id="name_fr" name="name_fr"></td></tr>
                    <tr><th><label for="desc_fr">Description (FR)</label></th><td><textarea id="desc_fr" name="desc_fr"></textarea></td></tr>
                    <tr><th><label for="type_fr">Type / Subtext (FR)</label></th><td><input type="text" id="type_fr" name="type_fr" placeholder="For wines"></td></tr>

                    <tr><td colspan="2"><hr></td></tr>

                    <tr>
                        <th><label for="product_image">Image</label></th>
                        <td><input type="file" id="product_image" name="product_image"></td>
                    </tr>
                    
                    <tr>    
                        <th><label for="product_location">Location</label></th>
                        <td>
                            <select id="product_location" name="product_location">
                                <option value="">Select Location</option>
                                <?php foreach($locations as $location): ?>
                                    <option value="<?php echo esc_attr($location->getID()); ?>">
                                        <?php echo esc_html($location->getCity()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2"><button type="submit" class="button button-primary" style="margin-top:10px;">Add Product</button></td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }
}