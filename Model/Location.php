<?php
namespace Pickups\Model;


class Location extends Model{

    const POST_TYPE = 'pickup_location';
    private $city;
    private $name;

    private $description;

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    


}