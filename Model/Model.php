<?php
namespace Pickups\Model;
abstract class Model{
    private $name;

    private $ID;

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
    }


    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }


    private function getPrivateProperties() {
        $reflect = new \ReflectionObject($this);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE);

        $result = [];
        // foreach ($props as $prop) {
        //     $prop->setAccessible(true);
        //     $result[$prop->getName()] = $prop->getValue($this);
        // }

        return $props;
    }


    public function __construct($post){
        if(!empty($post)){
            $this->setID($post->ID);
            $props = $this->getPrivateProperties();
            foreach ($props as $prop) {
                try{
                    if(property_exists($this, $prop->getName())){
                        $value = get_post_meta($post->ID, $prop->getName(), true);
                        //echo $prop->getName() . ":\n" . strval($value) . "\n";
                        $setterName = "set" . ucfirst($prop->getName());
                        
                        $this->$setterName($value);
                    }
                } catch (\Exception $e) {
                    echo "ReflectionException: " . $e->getMessage();
                    continue;
                }
                
            }
        }
        

    }

}