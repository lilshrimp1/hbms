<?php 

require_once 'model.php';
require_once 'Room.php';

class Amenity extends Model{
    protected static $table = 'amenities';

    public $id;
    public $name;
    public $price;
    public $description;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = []){
        foreach($data as $key => $value){
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }

    public static function all(){
        $results = parent::all();

        return $results
        ? array_map(fn ($user) => new self($user), $results)
        : null;
    }

    public static function find($id){
        $result = parent::find($id);

        return $result 
        ? new self($result)
        : null; 
    }


public static function getCurrentGuestInfo($amenity_id){
    $result = parent::getCurrentGuestInfo($amenity_id);
    
    if ($result) {
        // Convert associative array to object
        $guest = new \stdClass();
        foreach ($result as $key => $value) {
            $guest->$key = $value;
        }
        return $guest;
    }
    
    return null;
}

    public static function findStatus($status){
        $result = parent::findStatus($status);

        return $result 
        ? array_map(fn ($user) => new self($user), $result)
        : null; 
    }

    public static function isUsedInReservation($id){
        $result = parent::isUsedInReservation($id);

        return (is_array($result) && !empty($result))
        ? array_map(fn ($user) => new self($user), $result)
        : null; 
    }

    public static function findByColumn($column, $value){
        $result = parent::findByColumn($column, $value);

        return $result 
        ? new self($result)
        : null; 
    }

    public static function create(array $data){
        $result = parent::create($data);

        return $result
        ? new self($result)
        : null;
    }

    public function update(array $data){
        $result = parent::updateById($this->id, $data);

        if($result){
            foreach($data as $key => $value){
                if(property_exists($this, $key)){
                    $this->$key = $value;
                }
            }
        }
        else{
            return false;
        }
    }

    public function save() {
        $sql = "UPDATE amenities SET name = :name, price = :price, description = :description, status = :status WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute([
            ':name' => $this->name,
            ':price' => $this->price,
            ':description' => $this->description,
            ':status' => $this->status,
            ':id' => $this->id
        ]);
    }

    public function delete(){
        $result = parent::deleteById($this->id);

        if($result){
            foreach($this as $key => $value){
                if(property_exists($this, $key)){
                    unset($this->$key);
                }   
            }
        }
        else{
            return false;
        } 
    }

    public static function where($column, $operator, $value){
        $result = parent::where($column, $operator, $value);

        return $result 
        ? array_map(fn ($data) => new self($data), $result)
        : null;  
    }

    public function room_amenity(){
        $result = Room::where('amenity_id', '=', $this->id);

        return $result ?? null;
    }
}