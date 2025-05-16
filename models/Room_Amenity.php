<?php 

require_once 'model.php';
require_once 'Room.php';
require_once 'Amenity.php';

class Amenity extends Model{
    protected static $table = 'room_amenities';

    public $id;
    public $room_id;
    public $amenity_id;
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

    public static function findStatus($status){
        $result = parent::findStatus($status);

        return $result 
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

    public function save(){
        $data = [
            'room_id' => $this->room_id,
            'amenity_id' => $this->amenity_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        $this->update($data);
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

    public function amenity(){
        $result = Room::where('id', '=', $this->id);

        return $result ?? null;
    }
}