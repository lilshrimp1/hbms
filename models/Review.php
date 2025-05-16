<?php 
require_once 'model.php';
require_once 'User.php';
require_once 'Room.php';
require_once 'Payment.php';
require_once 'Reservation.php';

class Review extends Model{
    protected static $table = 'reviews';

    public $id;
    public $reservation_id;
    public $rating;
    public $comment;
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

    public static function countByStatus($status){
        return parent::countByStatus($status);
    }

    public static function countByStatusAndRating($status, $rating = null){
        return parent::countByStatusAndRating($status, $rating);
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
            'reservation_id' => $this->reservation_id,
            'comment' => $this->comment,
            'rating' => $this->rating,
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
    public function reservation(){
        $result = Reservation::where('id', '=', $this->reservation_id);

        return $result ?? null;
    }

}