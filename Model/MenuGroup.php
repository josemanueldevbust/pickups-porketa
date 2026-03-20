<?php
namespace Pickups\Model;


class MenuGroup extends Model{
    const POST_TYPE = 'pickup_menu_group';
    private $name;

    private $image;

    private $location;

    private $metadata = [];

    private $description;
    private $ID;


    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
    }

    public function getLocation() {
        return $this->location;
    }   

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


    public function getImage() {
        return $this->image;
    }

    public function getDescription(){
        return $this->description;
    }
    
    public function setDescription( $description) {
        $this->description = $description;
    }

    public function setMetadata($value) {
        $this->metadata = $value;
    }

    public function getMetadata($key) {
        
        // if(!empty($key) && isset($this->metadata[$key])){
        //     return $this->metadata[$key];
        // }
        return $this->metadata;
    }

}