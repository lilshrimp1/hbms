<?php
require_once '../Database/database.php';
require_once '../models/model.php';

class Guest extends Model {
    protected static $table = 'users'; // Assuming 'users' table holds guest information

    /**
     * Retrieves guest details by their ID.
     *
     * @param int $guestId The ID of the guest.
     * @return array|null An array containing the guest's details, or null if not found.
     */
    public static function getGuestDetails(int $guestId): ?array {
        return self::find($guestId);
    }

    /**
     * Retrieves all guests from the database.
     *
     * @return array|null An array of all guests, or null if there are no guests.
     */
    public static function getAllGuests(): ?array {
        return self::all();
    }

     /**
     * Updates a guest's information by their ID.
     *
     * @param int $guestId The ID of the guest to update.
     * @param array $data An associative array containing the columns and values to update.
     * Example: ['name' => 'John Doe', 'email' => 'john.doe@example.com']
     * @return array|null The updated guest data, or null on failure.
     */
    public static function updateGuestDetails(int $guestId, array $data): ?array {
        return self::updateById($guestId, $data);
    }

    /**
     * Deletes a guest from the database by their ID.
     *
     * @param int $guestId The ID of the guest to delete.
     * @return bool True on success, false on failure.
     */
    public static function deleteGuest(int $guestId): bool {
        return self::deleteById($guestId);
    }

    /**
     * Retrieves a guest's booking history.
     *
     * This assumes you have a 'reservations' table with a 'user_id' column
     * linking it to the 'users' table.
     *
     * @param int $guestId The ID of the guest.
     * @return array|null An array of the guest's bookings, or null if no bookings are found.
     */
    public static function getGuestBookings(int $guestId): ?array {
        try {
            $sql = "SELECT r.*, rm.room_number, rt.name as room_type_name
                    FROM reservations r
                    JOIN room rm ON r.room_id = rm.id
                    JOIN room_types rt ON rm.type_id = rt.id
                    WHERE r.user_id = ?";
            $stmt = self::$conn->prepare($sql);
            $stmt->bindValue(1, $guestId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: null; // Returns null if no results
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Gets the current guest's information based on amenity ID.
     *
     * @param int $amenityId The ID of the amenity.
     * @return array|null
     */
    public static function getCurrentGuestInfoByAmenity(int $amenityId): ?array{
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
        $stmt->execute([$amenityId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : null;
    }

      /**
     * Counts guests by status.
     *
     * @param string $status The status to filter by.
     * @return int The number of guests with the specified status.
     */
    public static function countGuestsByStatus(string $status): int {
        return self::countByStatus($status);
    }

    /**
     * Counts guests by status and date.
     *
     * @param string $status The status to filter by.
     * @param string|null $dateColumn The date column to filter on (e.g., 'created_at', 'last_login').
     * @param string $operator The comparison operator for the date (e.g., '=', '<', '>=').
     * @param string|null $dateValue The date value to compare against.
     * @return int
     */
    public static function countGuestsByStatusAndDate(string $status, ?string $dateColumn = null, string $operator = '=', ?string $dateValue = null): int {
        return self::countByStatusAndDate($status, $dateColumn, $operator, $dateValue);
    }



    /**
     * Fetches available rooms.
     *
     * @return array|null
     */
    public static function getAvailableRooms(): ?array {
        $sql = "SELECT r.id as room_id, r.room_number, rt.name as room_type_name, r.description, r.price, r.status, r.capacity
                  FROM room r
                  JOIN room_types rt ON r.type_id = rt.id
                  WHERE r.status = 'available'";
        try {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Fetches booking history.
     *
     * @return array|null
     */
    public static function getBookingHistory(): ?array {
        $sql = "SELECT res.id as reservation_id,
                       rt.name as room_type_name,
                       r.room_number,
                       res.check_in,
                       res.check_out
                 FROM reservations res
                 JOIN room r ON res.room_id = r.id
                 JOIN room_types rt ON r.type_id = rt.id
                 ORDER BY res.check_in DESC";
        try {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Fetches booking history details for modal.
     *
     * @return array|null
     */
    public static function getBookingHistoryDetails(): ?array {
        $sql = "SELECT res.id as reservation_id,
                       rt.name as room_type_name,
                       r.room_number,
                       res.check_in,
                       res.check_out,
                       res.guests,
                       res.status as reservation_status
                 FROM reservations res
                 JOIN room r ON res.room_id = r.id
                 JOIN room_types rt ON r.type_id = rt.id";
        try {
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}

class Dashboard extends Model {
    protected static $table = 'reservations';

    public static function getUpcomingBooking() {
        try {
            $sql = "SELECT r.*, rm.room_number, rt.name as type_name, GROUP_CONCAT(a.name) as amenities
                    FROM reservations r
                    JOIN room rm ON r.room_id = rm.id
                    JOIN room_types rt ON rm.type_id = rt.id
                    LEFT JOIN room_amenities ra ON rm.id = ra.room_id
                    LEFT JOIN amenities a ON ra.amenity_id = a.id
                    WHERE r.check_in >= CURDATE()
                    GROUP BY r.id
                    ORDER BY r.check_in
                    LIMIT 1";

            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            return $booking ? $booking : null;

        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function getTotalUpcomingBookings() {
        return self::countByStatusAndDate('confirmed', 'check_in', '>=', date('Y-m-d'));
    }

    public static function getTotalBookings() {
        try {
            $sql = "SELECT COUNT(*) as count FROM reservations";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $row['count'] : 0;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function getTotalAvailableRooms() {
        return Room::countByStatus('available');
    }
}

    
?>