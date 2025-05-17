<?php

class Model{
    protected static $conn;
    protected static $table;

    public static function setConnection($conn){
        self::$conn = $conn;
    }

    protected static function find($id){
        try{
            $sql = "SELECT * FROM "
                   . static::$table
                   . " WHERE id = ?";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindvalue(1, $id);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row : null;
        }
        catch(PDOException $e){
            die("Error: " . $e->getMessage());
        }
    }

    protected static function hasPastReservations($room_id) {
        $stmt = self::$conn->prepare("SELECT COUNT(*) FROM reservations WHERE room_id = ? AND check_out < NOW()");
        $stmt->execute([$room_id]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    protected static function isUsedInReservation($amenity_id) {
        // This query checks if the amenity is linked to any room that has been reserved
        $sql = "SELECT COUNT(*) FROM reservations r
                JOIN room_amenities ra ON r.room_id = ra.room_id
                WHERE ra.amenity_id = ?";
        
        $stmt = self::$conn->prepare($sql);
        $stmt->execute([$amenity_id]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    protected static function getCurrentGuestInfo($amenity_id) {
        $sql = "SELECT u.name, u.contact_no AS contact, r.check_in, r.check_out, r.guests AS guest_count
                FROM room_amenities ra
                JOIN room rm ON ra.room_id = rm.id
                JOIN reservations r ON r.room_id = rm.id
                JOIN users u ON r.user_id = u.id
                WHERE ra.amenity_id = ? 
                AND r.check_in <= CURRENT_DATE()
                AND r.check_out >= CURRENT_DATE()
                AND r.status = 'confirmed'
                LIMIT 1";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute([$amenity_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

    protected static function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE status = ?";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(1, $status);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row['count'] : 0;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    protected static function countByStatusAndDate($status, $date_column = null, $operator = '=', $date_value = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE status = ?";
            $params = [$status];

            if ($date_column && $date_value) {
                $sql .= " AND $date_column $operator ?";
                $params[] = $date_value;
            }

            $stmt = self::$conn->prepare($sql);
            foreach ($params as $i => $param) {
                $stmt->bindValue($i + 1, $param);
            }
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row['count'] : 0;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    protected static function countByStatusAndRating($status, $rating = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM " . static::$table . " WHERE status = ?";
            $params = [$status];

            if ($rating !== null) {
                $sql .= " AND rating = ?";
                $params[] = $rating;
            }

            $stmt = self::$conn->prepare($sql);
            foreach ($params as $i => $param) {
                $stmt->bindValue($i + 1, $param);
            }
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row['count'] : 0;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    protected static function findStatus($status) {
        try {
            $sql = "SELECT * FROM "
                   . static::$table
                   . " WHERE status = ?";
            
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(1, $status, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            return count($rows) > 0 ? $rows : null;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
    protected static function findByColumn($column, $value){
        try {
            $sql = "SELECT * FROM "
                   . static::$table
                   . " WHERE $column = ?";
            
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(1, $value);
            $stmt->execute();
            $row = $stmt->fetch();
            
            return $row ? $row : null;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    protected static function all(){
        try{
            $sql = "SELECT * FROM "
                   . static::$table
                   . " ORDER BY id ASC";
                   
            $stmt = self::$conn->query($sql);
            $rows = $stmt->fetchAll();
            return count($rows) > 0
                ? $rows
                : null;
        }
        catch(PDOException $e){
            die("Error: " . $e->getMessage());
        }
    }


    

    protected static function create(array $data){
        try {
            $columns = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_map(fn($key) => ":$key", array_keys($data)));

            $sql = "INSERT INTO "
                   . static::$table
                   . " ($columns) VALUES ($placeholders)";

            $stmt = self::$conn->prepare($sql);

            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $id = self::$conn->lastInsertId();
            return self::find($id);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    protected static function updateById($id, array $data){
        try{
            $set = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));

            $sql = "UPDATE "
                   . static::$table
                   . " SET $set WHERE id = :id";

            $stmt = self::$conn->prepare($sql);

            foreach($data as $key => $value){
                $stmt->bindvalue(":key", $value);
            }

            $stmt->bindvalue(":id", $id);
            $stmt->execute();
            return self::find($id);
        }
        catch(PDOException $e){
            die("Error: " . $e->getMessage());
        }
    }

    protected static function deleteById($id){
        try{
            $sql = "DELETE FROM "
                   . static::$table
                   . " WHERE id = :id";

            $stmt = self::$conn->prepare($sql);
            $stmt->bindvalue(':id', $id);
            return $stmt->execute();
        }
        catch(PDOException $e){
            die("Error: " . $e->getMessage());
        }
    }

    protected static function where($column, $operator, $value){
        try{
            $sql = "SELECT * FROM "
                   . static::$table
                   . " WHERE $column $operator :value";

            $stmt = self::$conn->prepare($sql);
            $stmt->bindvalue(':value', $value);
            $stmt->execute();
            $rows = $stmt->fetchAll();

            return count($rows) > 0
                ? $rows : null;
        }
        catch(PDOException $e){
            die("Error: " . $e->getMessage());
        }
    }
}

