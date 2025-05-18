<?php


class Modals {
    private $css;
    private $js;

    public function __construct($css = 'css.css', $js = 'Jscript.js') {
        $this->css = $css;
        $this->js = $js;
        $this->includeAssets();
    }

    private function includeAssets() {
        echo "<link rel='stylesheet' href='css.css'>";
        echo "<script src='Jscript.js'></script>";
    }

    public function layout($modal, $action) {
        $title = '';
        $buttonText = '';
        $actionUrl = '';
        $content = '';

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
                    $content = $this->bookingDetails();
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

        return "
        <div class='modal fade' id='{$modal}Modal' tabindex='-1' aria-labelledby='{$modal}ModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered modal-sm'>
                <div class='modal-content border-0' style='border-radius: 20px; background-color: #d3d3d3; min-width: 400px;'>
                    <div class='modal-body p-4'>
                        <h2 class='text-center mb-3' style='font-family: Cal Sans, sans-serif; font-weight: 700; font-size: 1.5rem;'>{$title}</h2>
                        {$content}
                        <div class='text-center'>
                            <button type='submit' class='btn text-white' style='background-color: #00b4b6; border-radius: 10px;'>{$buttonText}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
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

    private function bookingDetails() {
        return "
        <div class='d-flex gap-3'>
            <div style='flex: 1;'>
                <img src='room.jpg' alt='Room Image' style='width: 100%; border-radius: 20px;'>
            </div>
            <div style='flex: 1; background-color: #e8f5f8; padding: 15px; border-radius: 20px;'>
                <p>Full name:</p>
                <p>Contact number:</p>
                <p>Address:</p>
                <p>Room type:</p>
                <p>Check-in date:</p>
                <p>Check-out date:</p>
                <p>Amenities:</p>
                <p>Total Bill:</p>
                <p>Deposit:</p>
                <p>Booking status:</p>
            </div>
        </div>";
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

?>
