<?php
require_once '../models/model.php';

class Modals {
    private static $css = 'css.css';
    private static $js = 'Jscript.js';

    public function __construct($css = 'css.css', $js = 'Jscript.js') {
        self::$css = $css;
        self::$js = $js;
    }

    public static function layout($modal, $action, $data = []) {
        $title = '';
        $buttonText = '';
        
        $content = '';

        if ($modal === 'bookingDetails') {
            $title = 'Booking Details';
            $content = self::bookingDetails($data);
        } else {
            switch ($modal) {
                case 'create':
                    if ($action === 'register') {
                        $title = 'Register';
                        $buttonText = 'REGISTER';
                       
                        $content = self::registerForm();
                    } elseif ($action === 'book') {
                        $title = 'Book Room';
                        $buttonText = 'RESERVE';
                        
                        $content = self::bookingFormDetails($data);
                    } elseif ($action === 'feedback') {
                        $title = 'Feedback';
                        $buttonText = 'SUBMIT';
                        
                        $content = self::feedbackForm();
                    }
                    break;
                case 'update':
                    $title = 'Edit Profile';
                    $buttonText = 'Update';
                    
                    // Assuming $data['user_id'] contains the ID of the user to edit
                    $content = self::editProfileForm(isset($data['user_id']) ? $data['user_id'] : null);
                    break;
                default:
                    $title = 'Modal';
                    $buttonText = 'SUBMIT';
                   
                    $content = self::registerForm();
                    break;
            }
        }

        return "
        <div class='modal fade' id='{$modal}Modal' tabindex='-1' aria-labelledby='{$modal}ModalLabel' aria-hidden='true'>
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
        </style>";
    }

    private static function bookingFormDetails() {
        global $conn;

        $roomTypesQuery = "SELECT id, name FROM room_types";
        $roomTypesResult = $conn->query($roomTypesQuery);

        $amenitiesQuery = "SELECT id, name FROM amenities";
        $amenitiesResult = $conn->query($amenitiesQuery);

        $form = "<form id='bookingForm' method='post' action='process_booking.php' style='margin:0;'>
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
                        <select class='form-select' id='roomType' name='roomType' required onchange='updateRoomImage()'>";
        if ($roomTypesResult && $roomTypesResult->rowCount() > 0) {
            $roomTypesResult->execute();
            while ($roomType = $roomTypesResult->fetch(PDO::FETCH_ASSOC)) {
                $imgPath = ImagePaths::getRoomTypeImage($roomType['name']);
                $form .= "<option value='" . $roomType['id'] . "' data-img='" . $imgPath . "'>" . $roomType['name'] . "</option>";
            }
        } else {
            $form .= "<option value=''>No room types available</option>";
        }
        $form .= "</select>
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
                $form .= "<input type='checkbox'  id='amenity_" . $amenity['id'] . "' name='amenities[]' value='" . $amenity['id'] . "'>
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

    private static function editProfileForm($userId = null) {
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