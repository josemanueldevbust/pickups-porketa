<?php

namespace Pickups\Helpers;

class Data {
    // Class implementation goes here

    private static function map_post_to_object($class, $post) {
        $object = new $class($post);
        $object->setID($post->ID);
        // Map other metadata as needed
        return $object;
    }
    public static function fetch_items($class, $filters = []) {
        // Implementation for fetching items of the specified class with optional filters
        $args = [
            'post_type'      => $class::POST_TYPE, // Replace with your post type
            'posts_per_page' => -1,                 // -1 means all posts
            'post_status'    => 'publish',          // Only published posts
        ];

        $query = new \WP_Query($args);

        $items = [];

        if ($query->have_posts()) {
            $posts = $query->get_posts();
            

            foreach ($posts as $post) {
                $items[] = self::map_post_to_object( $class, $post);
            }
        } else {
            //echo 'No posts found.';
        }

        return $items;

    }

    public static function fetch_item_by_id($class, $id) {
        // Implementation for fetching a single item by ID
        $post = get_post($id);

        if ($post && $post->post_type === $class::POST_TYPE) {
            return self::map_post_to_object($class, $post);
        }

        return null;
    }

    public static function save_item($item) {
        // Implementation for saving an item
        $post_data = [
            'post_type'   => get_class($item)::POST_TYPE,
            'post_title'  => property_exists($item, 'name') ? $item->getName() : 'Untitled',
            'post_status' => 'publish',
        ];

        if(!empty($item->getID())){
            $post_data['ID'] = $item->getID();
            wp_update_post($post_data);
            $post_id = $post_data['ID'];
        }else{
            $post_id = wp_insert_post($post_data);
        }

        

        if (is_wp_error($post_id)) {
            return null;
        }

        // Save metadata
        $reflect = new \ReflectionObject($item);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE);

        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $value = $prop->getValue($item);
            update_post_meta($post_id, $prop->getName(), $value);
        }

        return $post_id;
    }

    public static function delete_item($id) {
        // Implementation for deleting an item by ID
        return wp_delete_post($id, true);
    }

    public static function create_rest($name, $callbacks){
        add_action('rest_api_init', function () use ($name, $callbacks) {
            if(isset($callbacks['get'])){
                register_rest_route('pickups/v1', "/$name/", [
                    'methods'  => 'GET',
                    'callback' => $callbacks['get'],
                    'permission_callback' => '__return_true', // Public access
                ]);
            }
            
            if(isset($callbacks['post'])){

                register_rest_route('pickups/v1', "/$name/", [
                    'methods'  => 'POST',
                    'callback' => $callbacks['post'],
                    'permission_callback' => '__return_true', // Public access
                ]);
            }

            if(isset($callbacks['delete'])){
                register_rest_route('pickups/v1', "/$name/", [
                    'methods'  => 'DELETE',
                    'callback' => $callbacks['delete'],
                    'permission_callback' => '__return_true', // Public access
                ]);
            }

            if(isset($callbacks['put'])){
                register_rest_route('pickups/v1', "/$name/", [
                    'methods'  => 'PUT',
                    'callback' => $callbacks['put'],
                    'permission_callback' => '__return_true', // Public access
                ]);
            }
            
        });

    }
}