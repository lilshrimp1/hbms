<?php 

require_once 'model.php';
require_once 'Amenity.php';
require_once 'RoomType.php';
require_once 'Reservation.php';

class Room extends Model{
    protected static $table = 'room';

    public $id;
    public $room_number;
    public $type_id;
    public $price;
    public $status;
    public $description;
    public $capacity;
    public $created_at;   

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

    public static function findByColumn($column, $value){
        $result = parent::findByColumn($column, $value);

        return $result 
        ? new self($result)
        : null; 
    }

    public static function countByStatus($status) {
        return parent::countByStatus($status);
    }


public static function getCurrentGuestInfo($room_id){
    $result = parent::getCurrentGuestInfo($room_id);

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


    public static function hasPastReservations($room_id) {
        $model = new parent();
        return $model->hasPastReservations($room_id);
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
        $sql = "UPDATE room SET 
                    room_number = :room_number, 
                    type_id = :type_id, 
                    price = :price, 
                    status = :status, 
                    description = :description, 
                    capacity = :capacity, 
                    created_at = :created_at 
                WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute([
            ':room_number' => $this->room_number,
            ':type_id' => $this->type_id,
            ':price' => $this->price,
            ':status' => $this->status,
            ':description' => $this->description,
            ':capacity' => $this->capacity,
            ':created_at' => $this->created_at,
            ':id' => $this->id
        ]);
    }

    public function delete(){
        try {
        $stmt = self::$conn->prepare("DELETE FROM room_amenities WHERE room_id = ?");
        $stmt->execute([$this->id]);

        $result = parent::deleteById($this->id);

        if($result){
            foreach($this as $key => $value){
                if(property_exists($this, $key)){
                    unset($this->$key);
                }   
            }
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Room deletion failed: " . $e->getMessage());
        return false;
    }
    }

    public static function where($column, $operator, $value){
        $result = parent::where($column, $operator, $value);

        return $result 
        ? array_map(fn ($data) => new self($data), $result)
        : null;  
    }

    public static function getRoomTypes() {
        try {
            $sql = "SELECT id, name FROM room_types";
            $stmt = self::$conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

}