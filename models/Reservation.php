<?php 
require_once 'model.php';
require_once 'User.php';
require_once 'Room.php';
require_once 'Payment.php';
require_once 'Review.php';

class Reservation extends Model{
    protected static $table = 'reservations';

    public $id;
    public $room_id;
    public $user_id;
    public $check_in;
    public $check_out;
    public $guests;
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

    public static function findByColumn($column, $value){
        $result = parent::findByColumn($column, $value);

        return $result 
        ? new self($result)
        : null; 
    }

    public static function countByStatusAndDate($status, $date_column = null, $operator = '=', $date_value = null){
        return parent::countByStatusAndDate($status, $date_column, $date_value);
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
            'user_id' => $this->user_id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'guests' => $this->guests,
            'status' => $this->status,
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
    public function user(){
        $result = Room::where('id', '=', $this->user_id);

        return $result ?? null;
    }

    public function room(){
        $result = Room::where('id', '=', $this->room_id);

        return $result ?? null;
    }

    public function payments(){
        $result = Payment::where('reservation_id', '=', $this->id);

        return $result ?? null;
    }

    public function review(){
        $result = Review::where('reservation_id', '=', $this->id);

        return $result ?? null;
    }

}