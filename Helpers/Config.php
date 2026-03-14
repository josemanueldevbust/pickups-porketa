<?php

namespace Pickups\Helpers;

class Config {

    public $class = "";
    public $name = "";
    public $title = "";


    function render_list() {
        //echo 'ADMIN PRODUCTS LIST';

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        echo '<div class="wrap"><h1>'.$this->title.' List</h1></div>';
        $class = "Pickups\Admin\\". $this->class . "List";
        $class::render();
    }

    function render_add_form() {
        //echo 'ADMIN ADD PRODUCT FORM';
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        echo '<div class="wrap"><h1>New '.$this->title.'</h1></div>';
        $class = "Pickups\Admin\\".$this->class . "Add";
        $class::render();   
    }
    function render_edit_form() {
       // echo 'ADMIN EDIT PRODUCT FORM';
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        echo '<div class="wrap"><h1>Edit '.$this->title.'</h1></div>';
        $class = "Pickups\Admin\\".$this->class . "Edit";
        $class::render();   
    }
    // Class implementation goes here
    public static function configure($class, $name, $title, $exclude=[]) {
        // Configuration settings go here
        $config = new Config(); 
        $config->class = $class;
        $config->name = $name;
        $config->title = $title;

        
            add_action('admin_menu', function () use ($config, $title, $exclude) {
                add_menu_page($title, $title, 'manage_options',
                 $config->name.'-list', 
                 function() use ($config){
                        $config->render_list();
                    }    // Callback function
                );
                if(!in_array('add', $exclude)){
                     add_submenu_page(
                        $config->name.'-list',       // Parent slug (e.g., Settings menu)
                        'Add ' . $title,           // Page title
                        'Add ' . $title,                // Menu title
                        'manage_options',            // Capability
                        'add-'.$config->name.'-form',           // Menu slug
                        function() use ($config){
                            $config->render_add_form();
                        }    // Callback function
                    );
                }
                
                if(!in_array('edit', $exclude)){
               
                        add_submenu_page(
                            null,                          // No parent menu — hidden from UI
                            'Edit ' . $title,           // Page title
                            'Edit ' . $title,           // Menu title (ignored)
                            'manage_options',              // Capability
                            'edit-'.$config->name.'-form',              // Slug
                            function() use ($config){
                                $config->render_edit_form();
                            }    // Callback function         // Callback
                    );
                }



           });
    }
}