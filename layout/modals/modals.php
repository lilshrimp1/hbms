<?php
require_once '../models/model.php';

class Modals {
    private static $css = 'css.css';
    private static $js = 'Jscript.js';

    public function __construct($css = 'css.css', $js = 'Jscript.js') {
        self::$css = $css;
        self::$js = $js;
    }


        public static function layout($modal, $data = []) {
    $title = '';
    $buttonText = '';
    $actionUrl = '#';
    $content = '';
    $modalId = '';

        switch ($modal) {
            case 'bookRoom':
                $modalId = 'bookRoomModal';
                $title = 'Book Room';
                $buttonText = 'RESERVE';
               
                $content = self::bookingFormDetails($data);
                break;
            case 'feedback':
                $modalId = 'feedbackModal';
                $title = 'Feedback';
                $buttonText = 'SUBMIT';
                
                $content = self::feedbackForm();
                break;
            case 'viewReservation':
                $modalId = 'viewReservationModal';
                $title = 'Reservation Details';
                $buttonText = '';
                $content = self::viewReservationDetails($data);
                break;
            case 'editReservation':
                $modalId = 'editReservationModal';
                $title = 'Edit Reservation';
                $buttonText = 'Update';
                $content = self::editReservationForm($data);
                break;
            case 'create':
                $modalId = 'createModal'; // Default create modal
                    $title = 'Register';
                    $buttonText = 'REGISTER';
                   
                    $content = self::registerForm();

            break;
        case 'update':
            $modalId = 'updateModal';
            $title = 'Edit Profile';
            $buttonText = 'Update';
            
            $content = self::editProfileForm(isset($data['user_id']) ? $data['user_id'] : null);
            break;
        default:
            $modalId = 'defaultModal';
            $title = 'Modal';    
            $content = self::bookingDetails($data);
            break;
    }

    return "
        <div class='modal fade' id='{$modalId}' tabindex='-1' aria-labelledby='{$modalId}Label' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered modal-sm'>
                <div class='modal-content border-0' style='border-radius: 20px; background-color: #d3d3d3; min-width: 400px;'>
                    <div class='modal-body p-4'>
                        <h2 class='text-center mb-3' style='font-family: Cal Sans, sans-serif; font-weight: 700; font-size: 1.5rem;'>{$title}</h2>
                        {$content}
                    </div>
                </div>
            </div>
        </div>";

    }

    private static function bookingDetails($data) {
        $roomType = isset($data['room_type_name']) ? htmlspecialchars($data['room_type_name']) : 'N/A';
        $roomNumber = isset($data['room_number']) ? htmlspecialchars($data['room_number']) : 'N/A';
        $checkIn = isset($data['check_in']) ? htmlspecialchars(date('m/d/Y', strtotime($data['check_in']))) : 'N/A';
        $checkOut = isset($data['check_out']) ? htmlspecialchars(date('m/d/Y', strtotime($data['check_out']))) : 'N/A';
        $guests = isset($data['guests']) ? htmlspecialchars($data['guests']) : 'N/A';
        $status = isset($data['reservation_status']) ? htmlspecialchars($data['reservation_status']) : 'N/A';

        return "
        <div class='booking-details'>
            <p>Room Type: <span>{$roomType}</span></p>
            <p>Room Number: <span>{$roomNumber}</span></p>
            <p>Check-in Date: <span>{$checkIn}</span></p>
            <p>Check-out Date: <span>{$checkOut}</span></p>
            <p>Number of Guests: <span>{$guests}</span></p>
            <p>Status: <span>{$status}</span></p>
        </div>
        <style>
            .booking-details p { margin-bottom: 5px; }
            .booking-details span { font-weight: bold; }
        ";
    }

    // Add AJAX form submission with SweetAlert notification for editReservationForm
    // Removed duplicate editReservationForm method to fix duplicate symbol error

    public static function editReservationForm($data) {
        if (empty($data)) {
            return "<p>No reservation data available.</p>";
        }

        $reservationId = htmlspecialchars($data['reservation_id'] ?? '');
        $roomType = htmlspecialchars($data['room_type_name'] ?? '');
        $roomNumber = htmlspecialchars($data['room_number'] ?? '');
        $checkIn = isset($data['check_in']) ? htmlspecialchars(date('Y-m-d', strtotime($data['check_in']))) : '';
        $checkOut = isset($data['check_out']) ? htmlspecialchars(date('Y-m-d', strtotime($data['check_out']))) : '';
        $guests = htmlspecialchars($data['guests'] ?? '');
        $status = htmlspecialchars($data['reservation_status'] ?? '');

        // For room types dropdown, fetch from DB
        global $conn;
        $roomTypesQuery = "SELECT id, name FROM room_types";
        $roomTypesResult = $conn->query($roomTypesQuery);

        $roomTypeOptions = "";
        if ($roomTypesResult && $roomTypesResult->rowCount() > 0) {
            while ($rt = $roomTypesResult->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($rt['name'] == $roomType) ? "selected" : "";
                $roomTypeOptions .= "<option value='" . htmlspecialchars($rt['id']) . "' $selected>" . htmlspecialchars($rt['name']) . "</option>";
            }
        }

        // Fetch user details for full_name, contact_number, address
        $fullName = '';
        $contactNumber = '';
        $address = '';
        if (!empty($data['user_id'])) {
            $userId = $data['user_id'];
            $stmt = $conn->prepare("SELECT name, contact_no, address FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $fullName = htmlspecialchars($user['name']);
                $contactNumber = htmlspecialchars($user['contact_no']);
                $address = htmlspecialchars($user['address']);
            }
        }

        return "
<form id='editReservationForm' method='post' action='../Reservation/update.php'>
            <input type='hidden' name='reservation_id' value='{$reservationId}'>
            <div class='mb-3'>
                <label for='fullName' class='form-label'>Full Name:</label>
                <input type='text' class='form-control' id='fullName' name='full_name' value='{$fullName}' required>
            </div>
            <div class='mb-3'>
                <label for='contactNumber' class='form-label'>Contact Number:</label>
                <input type='text' class='form-control' id='contactNumber' name='contact_number' value='{$contactNumber}' required>
            </div>
            <div class='mb-3'>
                <label for='address' class='form-label'>Address:</label>
                <input type='text' class='form-control' id='address' name='address' value='{$address}' required>
            </div>
            <div class='mb-3'>
                <label for='roomType' class='form-label'>Room Type:</label>
                <select class='form-select' id='roomType' name='room_type' required>
                    {$roomTypeOptions}
                </select>
            </div>
            <div class='mb-3'>
                <label for='roomNumber' class='form-label'>Room Number:</label>
                <input type='text' class='form-control' id='roomNumber' name='room_number' value='{$roomNumber}' readonly>
            </div>
            <div class='mb-3'>
                <label for='checkInDate' class='form-label'>Check-in Date:</label>
                <input type='date' class='form-control' id='checkInDate' name='check_in_date' value='{$checkIn}' required>
            </div>
            <div class='mb-3'>
                <label for='checkOutDate' class='form-label'>Check-out Date:</label>
                <input type='date' class='form-control' id='checkOutDate' name='check_out_date' value='{$checkOut}' required>
            </div>
            <div class='mb-3'>
                <label for='guests' class='form-label'>Number of Guests:</label>
                <input type='number' class='form-control' id='guests' name='number_of_guests' min='1' value='{$guests}' required>
            </div>
            <div class='mb-3'>
                <label for='status' class='form-label'>Status:</label>
                <select class='form-select' id='status' name='status' required>
                    <option value='pending' " . ($status == 'pending' ? 'selected' : '') . ">Pending</option>
                    <option value='confirmed' " . ($status == 'confirmed' ? 'selected' : '') . ">Confirmed</option>
                    <option value='cancelled' " . ($status == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                </select>
            </div>
            <div class='text-center'>
                <button type='submit' class='btn btn-primary'>Update Reservation</button>
            </div>
        </form>
        <style>
            #editReservationForm .form-label {
                font-weight: bold;
            }
        </style>
        ";
    }

    public static function viewReservationDetails($data) {
        if (empty($data)) {
            return "<p>No reservation data available.</p>";
        }
        $roomType = htmlspecialchars($data['room_type_name'] ?? 'N/A');
        $roomNumber = htmlspecialchars($data['room_number'] ?? 'N/A');
        $checkIn = isset($data['check_in']) ? htmlspecialchars(date('m/d/Y', strtotime($data['check_in']))) : 'N/A';
        $checkOut = isset($data['check_out']) ? htmlspecialchars(date('m/d/Y', strtotime($data['check_out']))) : 'N/A';
        $guests = htmlspecialchars($data['guests'] ?? 'N/A');
        $status = htmlspecialchars($data['reservation_status'] ?? 'N/A');

        return "
        <div class='view-reservation-details'>
            <p>Room Type: <span>{$roomType}</span></p>
            <p>Room Number: <span>{$roomNumber}</span></p>
            <p>Check-in Date: <span>{$checkIn}</span></p>
            <p>Check-out Date: <span>{$checkOut}</span></p>
            <p>Number of Guests: <span>{$guests}</span></p>
            <p>Status: <span>{$status}</span></p>
        </div>
        <style>
            .view-reservation-details p { margin-bottom: 5px; }
            .view-reservation-details span { font-weight: bold; }
        </style>";
    }

    public static function viewReservationForm($data) {
        if (empty($data)) {
            return "<p>No reservation data available.</p>";
        }

        $reservationId = htmlspecialchars($data['reservation_id'] ?? '');
        $roomType = htmlspecialchars($data['room_type_name'] ?? '');
        $roomNumber = htmlspecialchars($data['room_number'] ?? '');
        $checkIn = isset($data['check_in']) ? htmlspecialchars(date('Y-m-d', strtotime($data['check_in']))) : '';
        $checkOut = isset($data['check_out']) ? htmlspecialchars(date('Y-m-d', strtotime($data['check_out']))) : '';
        $guests = htmlspecialchars($data['guests'] ?? '');
        $status = htmlspecialchars($data['reservation_status'] ?? '');
        $totalDue = htmlspecialchars(number_format($data['total_due'] ?? 0, 2));
        // For room types dropdown, fetch from DB
        global $conn;
        $roomTypesQuery = "SELECT id, name FROM room_types";
        $roomTypesResult = $conn->query($roomTypesQuery);

        $roomTypeOptions = "";
        if ($roomTypesResult && $roomTypesResult->rowCount() > 0) {
            while ($rt = $roomTypesResult->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($rt['name'] == $roomType) ? "selected" : "";
                $roomTypeOptions .= "<option value='" . htmlspecialchars($rt['id']) . "' $selected>" . htmlspecialchars($rt['name']) . "</option>";
            }
        }

        return "
<form id='viewReservationForm' method='post' action='#'>
            <input type='hidden' name='reservation_id' value='{$reservationId}'>
            <div class='mb-3'>
                <label for='roomType' class='form-label'>Room Type:</label>
                <select class='form-select' id='roomType' name='roomType' disabled>
                    {$roomTypeOptions}
                </select>
            </div>
            <div class='mb-3'>
                <label for='roomNumber' class='form-label'>Room Number:</label>
                <input type='text' class='form-control' id='roomNumber' name='roomNumber' value='{$roomNumber}' readonly>
            </div>
            <div class='mb-3'>
                <label for='checkInDate' class='form-label'>Check-in Date:</label>
                <input type='date' class='form-control' id='checkInDate' name='checkInDate' value='{$checkIn}' readonly>
            </div>
            <div class='mb-3'>
                <label for='checkOutDate' class='form-label'>Check-out Date:</label>
                <input type='date' class='form-control' id='checkOutDate' name='checkOutDate' value='{$checkOut}' readonly>
            </div>
            <div class='mb-3'>
                <label for='guests' class='form-label'>Number of Guests:</label>
                <input type='number' class='form-control' id='guests' name='guests' min='1' value='{$guests}' readonly>
            </div>
            <div class='mb-3'>
                <label for='status' class='form-label'>Status:</label>
                <select class='form-select' id='status' name='status' disabled>
                    <option value='pending' " . ($status == 'pending' ? 'selected' : '') . ">Pending</option>
                    <option value='confirmed' " . ($status == 'confirmed' ? 'selected' : '') . ">Confirmed</option>
                    <option value='cancelled' " . ($status == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                </select>
            </div>
            <div class='mb-3'>
                <label for='totalDue' class='form-label'>Total Due: </label>
                <input type='number' class='form-control' id='totalDue' name=totalDue' value='{$totalDue}' readonly>
            </div>
        </form>
        <style>
            #viewReservationForm .form-label {
                font-weight: bold;
            }
        </style>
        ";
    }

    private static function bookingFormDetails($preselectedRoomTypeId = null) {
        global $conn;

        $roomTypesQuery = "SELECT id, name FROM room_types";
        $roomTypesResult = $conn->query($roomTypesQuery);

        $amenitiesQuery = "SELECT id, name FROM amenities";
        $amenitiesResult = $conn->query($amenitiesQuery);

        // Hardcoded prices for rooms and amenities for demo purposes
        $roomPrices = [
            1 => 1000, // single bed room
            2 => 1500, // two bed room
            3 => 2000, // family room
            4 => 3000, // deluxe room
        ];

        $amenityPrices = [
            1 => 100, // example amenity id 1
            2 => 200, // example amenity id 2
            3 => 150, // example amenity id 3
            4 => 250, // example amenity id 4
        ];

$form = "<form id='bookingForm' method='post' action='../guest/process_booking.php' style='margin:0;'>
            <div class='booking-modal-flex'>
                <div class='booking-modal-image'>
                    <img id='roomTypeImage' src='" . ImagePaths::getRoomTypeImage(
                        $roomTypesResult && $roomTypesResult->rowCount() > 0
                            ? $roomTypesResult->fetch(PDO::FETCH_ASSOC)['name']
                            : ''
                    ) . "' alt='Room Image' style='width: 350px; height: auto; border-radius: 20px;' >
                </div>
                <br>
                <div class='booking-modal-form'>
                    <div class='mb-3'>
                        <label for='fullName' class='form-label'>Full Name:</label>
                        <input type='text' class='form-control' id='fullName' name='fullName' required>
                    </div>
                    <div class='mb-3'>
                        <label for='contactNumber' class='form-label'>Contact Number:</label>
                        <input type='text' class='form-control' id='contactNumber' name='contactNumber' required>
                    </div>
                    <div class='mb-3'>
                        <label for='address' class='form-label'>Address:</label>
                        <textarea class='form-control' id='address' name='address' rows='3' required></textarea>
                    </div>
                    <div class='mb-3'>
                        <label for='roomType' class='form-label'>Room Type:</label>
                        <select class='form-select' id='roomType' name='roomType' required onchange='updateRoomImage(); calculateTotalBill();'>";

        if ($roomTypesResult && $roomTypesResult->rowCount() > 0) {
            $roomTypesResult->execute();
            while ($roomType = $roomTypesResult->fetch(PDO::FETCH_ASSOC)) {
                $imgPath = ImagePaths::getRoomTypeImage($roomType['name']);
                $selected = ($preselectedRoomTypeId && $roomType['id'] == $preselectedRoomTypeId) ? "selected" : "";
                $price = isset($roomPrices[$roomType['id']]) ? $roomPrices[$roomType['id']] : 0;
                $form .= "<option value='" . $roomType['id'] . "' data-img='" . $imgPath . "' data-price='" . $price . "' $selected>" . $roomType['name'] . "</option>";
            }
        } else {
            $form .= "<option value=''>No room types available</option>";
        }
        $form .= "</select>
                    </div>
                    <div class='mb-3'>
                        <label for='numberOfGuests' class='form-label'>Number of Guests:</label>
                        <input type='number' class='form-control' id='numberOfGuests' name='numberOfGuests' min='1' value='1' required onchange='calculateTotalBill()'>
                    </div>
                    <div class='mb-3'>
                        <label for='checkInDate' class='form-label'>Check-in Date:</label>
                        <input type='date' class='form-control' id='checkInDate' name='checkInDate' required>
                    </div>
                    <div class='mb-3'>
                        <label for='checkOutDate' class='form-label'>Check-out Date:</label>
                        <input type='date' class='form-control' id='checkOutDate' name='checkOutDate' required>
                    </div>
                    <div class='mb-3'>
                        <label for='amenities' class='form-label'>Amenities:</label><br>";
        if ($amenitiesResult && $amenitiesResult->rowCount() > 0) {
            while ($amenity = $amenitiesResult->fetch(PDO::FETCH_ASSOC)) {
                $price = isset($amenityPrices[$amenity['id']]) ? $amenityPrices[$amenity['id']] : 0;
                $form .= "<input type='checkbox' class='amenity-checkbox' id='amenity_" . $amenity['id'] . "' name='amenities[]' value='" . $amenity['id'] . "' data-price='" . $price . "' onchange='calculateTotalBill()'>
                                <label for='amenity_" . $amenity['id'] . "'>" . $amenity['name'] . "</label><br>";
            }
        } else {
            $form .= "No amenities available";
        }
        $form .= "</div>
                    <div class='mb-3'>
                        <label for='totalBill' class='form-label'>Total Bill:</label>
                        <div class='input-group'>
                            <span class='input-group-text'>₱</span>
                            <input type='text' class='form-control' id='totalBill' name='totalBill' readonly>
                        </div>
                    </div>
                    <div class='input-group'>
                        <span class='input-group-text'>₱</span>
                        <input type='text' class='form-control' id='deposit' name='deposit'>
                    </div>
                    <br>
                    <button type='submit' class='btn btn-primary'>Submit Booking</button>
                </div>
            </div>
        </form>";

        $form .= "
        <script>
        function updateRoomImage() {
            var select = document.getElementById('roomType');
            var img = document.getElementById('roomTypeImage');
            var selected = select.options[select.selectedIndex];
            var imgPath = selected.getAttribute('data-img');
            img.src = imgPath;
        }

        function calculateTotalBill() {
            var roomSelect = document.getElementById('roomType');
            var amenityCheckboxes = document.querySelectorAll('.amenity-checkbox');
            var totalBillInput = document.getElementById('totalBill');

            var roomPrice = 0;
            if (roomSelect) {
                var selectedOption = roomSelect.options[roomSelect.selectedIndex];
                roomPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            }

            var amenitiesTotal = 0;
            amenityCheckboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    amenitiesTotal += parseFloat(checkbox.getAttribute('data-price')) || 0;
                }
            });

            var total = roomPrice + amenitiesTotal;
            totalBillInput.value = total.toFixed(2);
        }
        </script>";

        return $form;
    }

    private static function registerForm() {
        return "
        <form action='#' method='POST'>
            <div class='mb-2'>
                <label class='form-label'>Full Name:</label>
                <input type='text' name='name' class='form-control signup-input' required>
            </div>
            <div class='mb-2'>
                <label class='form-label'>Contact Number:</label>
                <input type='text' name='contact' class='form-control signup-input' required>
            </div>
            <div class='mb-2'>
                <label class='form-label'>Address:</label>
                <input type='text' name='address' class='form-control signup-input' required>
            </div>
            <div class='mb-2'>
                <label class='form-label'>Email:</label>
                <input type='email' name='email' class='form-control signup-input' required>
            </div>
            <div class='mb-2'>
                <label class='form-label'>Password:</label>
                <input type='password' name='password' class='form-control signup-input' required>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Confirm Password:</label>
                <input type='password' name='confirm_password' class='form-control signup-input' required>
            </div>
        </form>";
    }

    private static function feedbackForm() {
        global $conn;

        // Fetch the latest 5 approved reviews and join with users for the name
        $feedbackHtml = "<div class='feedback-list'>";
        $sql = "
            SELECT
                u.name AS user_name,
                r.created_at,
                r.rating,
                r.comment
            FROM reviews r
            JOIN reservations res ON r.reservation_id = res.id
            JOIN users u ON res.user_id = u.id
            WHERE r.status = 'approved'
            ORDER BY r.created_at DESC
            LIMIT 5
        ";
        $stmt = $conn->query($sql);
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stars = str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5 - (int)$row['rating']);
                $date = date('m/d/Y', strtotime($row['created_at']));
                $feedbackHtml .= "
                    <div class='feedback-item'>
                        <p><strong>" . htmlspecialchars($row['user_name']) . "</strong> - $date - $stars</p>
                        <p>" . htmlspecialchars($row['comment']) . "</p>
                    </div>
                ";
            }
        } else {
            $feedbackHtml .= "<div class='feedback-item'><p>No feedback yet.</p></div>";
        }
        $feedbackHtml .= "</div>";

        // Feedback form
        $feedbackHtml .= "
        <div class='rating mt-3 text-center'>
            <form action='' method='POST'>
                <div class='star-rating'>
                    <input type='radio' id='star5' name='rating' value='5' required /><label for='star5'>★</label>
                    <input type='radio' id='star4' name='rating' value='4' /><label for='star4'>★</label>
                    <input type='radio' id='star3' name='rating' value='3' /><label for='star3'>★</label>
                    <input type='radio' id='star2' name='rating' value='2' /><label for='star2'>★</label>
                    <input type='radio' id='star1' name='rating' value='1' /><label for='star1'>★</label>
                </div>
                <div class='mt-3'>
                    <input type='text' class='form-control' placeholder='Tell us about your experience!' name='comment' required>
                </div>
                <div class='mt-2 text-center'>
                    <button class='btn btn-primary' type='submit' name='submit_feedback'>Submit</button>
                </div>
            </form>
        </div>";

        return $feedbackHtml;
    }

    private static function editProfileForm($userId) {
        if (!$userId) {
            return "<p>Error: User ID not provided.</p>";
        }

        try {
            // Assuming the database connection is already established globally or through Model::setConnection()
            global $conn;
            if (!$conn) {
                // Handle the case where the connection is not established
                return "<p>Error: Database connection not established.</p>";
            }

            // Use the User model to fetch user data
            $user = User::find($userId);

            if ($user) {
                $name = htmlspecialchars($user['name']);
                $contact = htmlspecialchars($user['contact_no']);
                $address = htmlspecialchars($user['address']);

                return "
                <form action='update.php' method='POST'>
                    <input type='hidden' name='user_id' value='" . htmlspecialchars($userId) . "'>
                    <div class='mb-3'>
                        <label class='form-label'>Full Name:</label>
                        <input type='text' name='name' class='form-control signup-input' value='$name'>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label'>Contact Number:</label>
                        <input type='text' name='contact' class='form-control signup-input' value='$contact'>
                    </div>
                    <div class='mb-3'>
                        <label class='form-label'>Address:</label>
                        <input type='text' name='address' class='form-control signup-input' value='$address'>
                    </div>
                    <div class='text-center'>
                        <button type='submit' class='btn btn-primary'>Update Profile</button>
                    </div>
                </form>";
            } else {
                return "<p>No user data found for ID: " . htmlspecialchars($userId) . "</p>";
            }

        } catch (PDOException $e) {
            return "<p>Error fetching user data: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

class ImagePaths {
    private static $baseImagePath = "../images/";

    public static function getRoomTypeImage($room_type_name) {
        // Normalize for case-insensitive matching
        $normalized = strtolower(trim($room_type_name));
        $imageFiles = [
            'single bed room' => 'single_bedroom.jpeg',
            'two bed room'     => 'two_bedroom.png',
            'family room'       => 'family_bedroom.png',
            'deluxe room'       => 'suite.png',
        ];

        if (isset($imageFiles[$normalized])) {
            return self::$baseImagePath . $imageFiles[$normalized];
        } else {
            return self::$baseImagePath . 'default_room_type.jpg';
        }
    }
}

?>