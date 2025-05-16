<?php 

require_once 'model.php';
require_once 'Room.php';

class RoomType extends Model{
    protected static $table = 'room_types';

    public $id;
    public $name;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = []){
        foreach($data as $key => $value){
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }

    public static function find($id) {
        $result = parent::find($id);

        return $result 
            ? new self((array) $result) // Convert to object
            : null; 
    }

    public static function getRoomTypes() {
        $result = parent::all();

        return $result 
            ? array_map(fn ($data) => new self($data), $result)
            : null; 
    } 

    public static function where($column, $operator, $value){
        $result = parent::where($column, $operator, $value);

        return $result 
        ? array_map(fn ($data) => new self($data), $result)
        : null;  
    }

    public function roomTypes(){
        $result = Room::where('type_id', '=', $this->id);

        return $result ?? null;
    }
}