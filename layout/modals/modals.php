<?php

class Modals {
    private $css = 'css.css';
    private $js = 'Jscript.js';

    public function __construct($css = 'css.css', $js = 'Jscript.js') {
        $this->css = $css;
        $this->js = $js;
    }

    public function layout($modal, $action, $data = []) {
        $title = '';
        $buttonText = '';
        $actionUrl = '#';
        $content = '';

        if ($modal === 'bookingDetails') {
            $title = 'Booking Details';
            $content = $this->bookingDetails($data); // Display booking details
        } else {
            switch ($modal) {
                case 'create':
                    if ($action === 'register') {
                        $title = 'Register';
                        $buttonText = 'REGISTER';
                        $actionUrl = 'signup.php';
                        $content = $this->registerForm();
                    } elseif ($action === 'book') {
                        $title = 'Book Room';
                        $buttonText = 'RESERVE';
                        $actionUrl = 'booking.php';
                        $content = $this->bookingFormDetails($data); // Display booking form details
                    } elseif ($action === 'feedback') {
                        $title = 'Feedback';
                        $buttonText = 'SUBMIT';
                        $actionUrl = 'feedback.php';
                        $content = $this->feedbackForm();
                    }
                    break;
                case 'update':
                    $title = 'Edit Profile';
                    $buttonText = 'Update';
                    $actionUrl = 'update.php';
                    $content = $this->editProfileForm();
                    break;
                default:
                    $title = 'Modal';
                    $buttonText = 'SUBMIT';
                    $actionUrl = '#';
                    $content = $this->registerForm();
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

    private function bookingDetails($data) { // Display booking details
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

        private function bookingFormDetails() { // Display booking form details
        global $conn;

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Fetch room types from the database
        $roomTypesQuery = "SELECT id, name FROM room_types";
        $roomTypesResult = mysqli_query($conn, $roomTypesQuery);

        // Fetch amenities from the database
        $amenitiesQuery = "SELECT id, name FROM amenities";
        $amenitiesResult = mysqli_query($conn, $amenitiesQuery);

        // Start the form
        $form = "<form id='bookingForm' method='post' action='process_booking.php'>
            <div class='d-flex gap-3'>
                <div style='flex: 1;'>
                    <img src='room.jpg' alt='Room Image' style='width: 100%; border-radius: 20px;'>
                </div>
                <div style='flex: 1; background-color: #e8f5f8; padding: 15px; border-radius: 20px;'>
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
                        <select class='form-select' id='roomType' name='roomType' required>";
        if (mysqli_num_rows($roomTypesResult) > 0) {
            while ($roomType = mysqli_fetch_assoc($roomTypesResult)) {
                $form .= "<option value='" . $roomType['id'] . "'>" . $roomType['name'] . "</option>";
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
        if (mysqli_num_rows($amenitiesResult) > 0) {
            while ($amenity = mysqli_fetch_assoc($amenitiesResult)) {
                $form .= "<input type='checkbox'  id='amenity_" . $amenity['id'] . "' name='amenities[]' value='" . $amenity['id'] . "'>
                        <label for='amenity_" . $amenity['id'] . "'>" . $amenity['name'] . "</label><br>";
            }
        } else {
            $form .= "No amenities available";
        }
        $form .= "</div>
                    <div class='mb-3'>
                        <label for='totalBill' class='form-label'>Total Bill:</label>
                        <input type='text' class='form-control' id='totalBill' name='totalBill' readonly>
                    </div>
                    <div class='mb-3'>
                        <label for='deposit' class='form-label'>Deposit:</label>
                        <input type='text' class='form-control' id='deposit' name='deposit'>
                    </div>
                    <div class='mb-3'>
                        <label for='bookingStatus' class='form-label'>Booking Status:</label>
                        <select class='form-select' id='bookingStatus' name='bookingStatus'>
                            <option value='pending'>Pending</option>
                            <option value='confirmed'>Confirmed</option>
                            <option value='cancelled'>Cancelled</option>
                        </select>
                    </div>
                    <button type='submit' class='btn btn-primary'>Submit Booking</button>
                </div>
            </div>
        </form>";

        return $form;
    }


    private function registerForm() {
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

    private function feedbackForm() {
        return "
        <div class='feedback-list'>
            <div class='feedback-item'>
                <p><strong>Moses Alfonso</strong> - 05/05/2025 - ★★★★☆</p>
                <p>May calamansi ung adobo nila!</p>
            </div>
            <div class='feedback-item'>
                <p><strong>Moses Alfonso</strong> - 05/05/2025 - ★★★☆☆</p>
                <p>Mabaho banyo nila!</p>
            </div>
        </div>
        <div class='rating mt-3 text-center'>
            <form action='feedback.php' method='POST'>
                <div class='star-rating'>
                    <input type='radio' id='star5' name='rating' value='5' /><label for='star5'>★</label>
                    <input type='radio' id='star4' name='rating' value='4' /><label for='star4'>★</label>
                    <input type='radio' id='star3' name='rating' value='3' /><label for='star3'>★</label>
                    <input type='radio' id='star2' name='rating' value='2' /><label for='star2'>★</label>
                    <input type='radio' id='star1' name='rating' value='1' /><label for='star1'>★</label>
                </div>
                <div class='mt-3'>
                    <input type='text' class='form-control' placeholder='Tell us about your experience!' name='feedback'>
                </div>
                <div class='mt-2 text-center'>
                    <button class='btn btn-primary'>Submit</button>
                </div>
            </form>
        </div>";
    }

    private function editProfileForm() {
        return "
        <form action='update.php' method='POST'>
            <div class='mb-3'>
                <label class='form-label'>Full Name:</label>
                <input type='text' name='name' class='form-control signup-input' value='Moses Alfonso' readonly>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Contact Number:</label>
                <input type='text' name='contact' class='form-control signup-input' value='09196795916' readonly>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Address:</label>
                <input type='text' name='address' class='form-control signup-input' value='Brgy. Tondod, San Jose City' readonly>
            </div>
        </form>";
    }
}

class ImagePaths {
    private static $baseImagePath = "../images/";

    public static function getRoomTypeImage($room_type_name) {
        $imageFiles = [
            'Single Bed Room' => 'single_bedroom.jpeg',
            'Two Bed Room' => 'two_bedroom.png',
            'Family Room' => 'family_bedroom.png',
            'Deluxe Room' => 'suite.png',
            // ... add more room type to image mappings as needed
        ];

        if (array_key_exists($room_type_name, $imageFiles)) {
            return self::$baseImagePath . $imageFiles[$room_type_name];
        } else {
            return self::$baseImagePath . 'default_room_type.jpg';
        }
    }
}
?>