<?php
namespace Pickups\Model;

class Product extends Model {
    const POST_TYPE = 'pickup_product';
    
    private $ID;
    private $category;
    private $subcategory;
    private $price;
    
    private $name_es;
    private $desc_es;
    private $type_es;
    
    private $name_en;
    private $desc_en;
    private $type_en;
    
    private $name_it;
    private $desc_it;
    private $type_it;
    
    private $name_ca;
    private $desc_ca;
    private $type_ca;

    private $name_fr;
    private $desc_fr;
    private $type_fr;
    
    private $image;
    private $location;

    // Getters and Setters
    public function getID() { return $this->ID; }
    public function setID($ID) { $this->ID = $ID; }
    
    // NOTE: Name is inherited/required by Data::save_item, we fallback to name_es
    public function getName() { return $this->name_es; }
    public function setName($name) { /* no-op or default */ }

    public function getCategory() { return $this->category; }
    public function setCategory($category) { $this->category = $category; }

    public function getSubcategory() { return $this->subcategory; }
    public function setSubcategory($subcategory) { $this->subcategory = $subcategory; }

    public function getPrice() { return $this->price; }
    public function setPrice($price) { $this->price = $price; }

    public function getName_es() { return $this->name_es; }
    public function setName_es($name_es) { $this->name_es = $name_es; }

    public function getDesc_es() { return $this->desc_es; }
    public function setDesc_es($desc_es) { $this->desc_es = $desc_es; }

    public function getType_es() { return $this->type_es; }
    public function setType_es($type_es) { $this->type_es = $type_es; }

    public function getName_en() { return $this->name_en; }
    public function setName_en($name_en) { $this->name_en = $name_en; }

    public function getDesc_en() { return $this->desc_en; }
    public function setDesc_en($desc_en) { $this->desc_en = $desc_en; }

    public function getType_en() { return $this->type_en; }
    public function setType_en($type_en) { $this->type_en = $type_en; }

    public function getName_it() { return $this->name_it; }
    public function setName_it($name_it) { $this->name_it = $name_it; return $this; }
    
    public function getDesc_it() { return $this->desc_it; }
    public function setDesc_it($desc_it) { $this->desc_it = $desc_it; return $this; }
    
    public function getType_it() { return $this->type_it; }
    public function setType_it($type_it) { $this->type_it = $type_it; return $this; }

    public function getName_ca() { return $this->name_ca; }
    public function setName_ca($name_ca) { $this->name_ca = $name_ca; return $this; }
    
    public function getDesc_ca() { return $this->desc_ca; }
    public function setDesc_ca($desc_ca) { $this->desc_ca = $desc_ca; return $this; }
    
    public function getType_ca() { return $this->type_ca; }
    public function setType_ca($type_ca) { $this->type_ca = $type_ca; return $this; }

    public function getName_fr() { return $this->name_fr; }
    public function setName_fr($name_fr) { $this->name_fr = $name_fr; return $this; }
    
    public function getDesc_fr() { return $this->desc_fr; }
    public function setDesc_fr($desc_fr) { $this->desc_fr = $desc_fr; return $this; }
    
    public function getType_fr() { return $this->type_fr; }
    public function setType_fr($type_fr) { $this->type_fr = $type_fr; return $this; }

    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }

    public function getLocation() { return $this->location; }
    public function setLocation($location) { $this->location = $location; }
}