<?php
namespace Pickups\Admin;

use ElementorDeps\Twig\Cache\NullCache;
use Pickups\Model\Model;
use Pickups\Model\Location;
use Pickups\Helpers\Data;
class LocationEdit {
    // Class implementation goes here

    public static function render() {

        $postId = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
        $location = Data::fetch_item_by_id(Location::class, $postId);


        $url = '';
        $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        


        $location->setName($data['location_name']);
        $location->setCity($data['location_city']);
        $location->setDescription($data['location_description']);

        try {
            Data::save_item($location);
            echo '<div class="notice notice-success is-dismissible"><p>Location added successfully!</p></div>';
        } catch (\Exception $e) {
            echo '<div class="notice notice-error is-dismissible"><p>Error adding location: ' . $e->getMessage() . '</p></div>';
        }
    


        ?>
        <div class="wrap">
            <h1>Edit Location</h1>
            <form enctype="multipart/form-data" method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="location_name">Location Name</label></th>
                        <td><input type="text" value="<?php echo $location->getName(); ?>" id="location_name" name="location_name" required></td>
                    </tr>
                    <tr>
                        <th><label for="location_city">City</label></th>
                        <td><input type="text" value="<?php echo $location->getCity(); ?>" id="location_name" name="location_name" required></td>
                    </tr>
                    <tr>
                        <th><label for="location_description">Description</label></th>
                        <td><textarea id="location_description" value="<?php echo $location->getDescription(); ?>" name="location_description">
                            <?php echo $location->getDescription(); ?>
                        </textarea></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="submit">Save Location</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php

    }

}