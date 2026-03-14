<?php
namespace Pickups\Model;
class Customer extends Model {
    const POST_TYPE = 'pickup_customer';
    private $firstName;
    private $lastName;
    private $email;

    private $phoneNumber;

    private $address;

    private $metadata;



    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    
    }
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    



    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    public function setAddress($address) {
        $this->address = $address;
    }
    public function getAddress() {
        return $this->address;
    }
    public function setMetadata($key, $value) {
        $this->metadata[$key] = $value;
    }
    public function getMetadata($key) {
        return $this->metadata[$key];
    }
}