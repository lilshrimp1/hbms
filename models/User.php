<?php 
require_once 'model.php';
require_once 'Reservation.php';

class User extends Model{
    protected static $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $status;
    public $created_at;
    public $updated_at;
    public $contact_no;
    public $address;

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

    public function save() {
    if (isset($this->id) && !empty($this->id)) {
        // If user has id, update existing user
        $sql = "UPDATE users SET 
                    name = :name, 
                    email = :email, 
                    password = :password, 
                    role = :role, 
                    status = :status, 
                    created_at = :created_at, 
                    updated_at = :updated_at, 
                    contact_no = :contact_no, 
                    address = :address 
                WHERE id = :id";
        $stmt = self::$conn->prepare($sql);
        return $stmt->execute([
            ':name' => $this->name,
            ':email' => $this->email,
            ':password' => $this->password,
            ':role' => $this->role,
            ':status' => $this->status,
            ':created_at' => $this->created_at,
            ':updated_at' => $this->updated_at,
            ':contact_no' => $this->contact_no,
            ':address' => $this->address,
            ':id' => $this->id
        ]);
    } else {
        // No id yet — insert new user
        $sql = "INSERT INTO users 
                (name, email, password, role, status, created_at, updated_at, contact_no, address)
                VALUES 
                (:name, :email, :password, :role, :status, :created_at, :updated_at, :contact_no, :address)";
        $stmt = self::$conn->prepare($sql);
        $result = $stmt->execute([
            ':name' => $this->name,
            ':email' => $this->email,
            ':password' => $this->password,
            ':role' => $this->role,
            ':status' => $this->status,
            ':created_at' => $this->created_at,
            ':updated_at' => $this->updated_at,
            ':contact_no' => $this->contact_no,
            ':address' => $this->address
        ]);

        if ($result) {
            // Assign the new ID to the object
            $this->id = self::$conn->lastInsertId();
        }

        return $result;
    }
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

    public function reservations(){
        return Reservation::where('user_id', '=', $this->id);
    }
}