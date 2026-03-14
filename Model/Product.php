<?php
namespace Pickups\Model;


class Product extends Model{
    const POST_TYPE = 'pickup_product';
    private $name;
    private $price;

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

    public function getPrice() {
        return $this->price;
    }

    public function setImage($image) {
        $this->image = $image;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function getDescription() {
        return $this->description;
    }
    public function setPrice($price) {
        $this->price = $price;
    }


    public function getImage() {
        return $this->image;
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