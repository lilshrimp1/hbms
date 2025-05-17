<?php

class ReservationCard {
    private $guestName;
    private $bookingNumber;
    private $roomNumber;
    private $date;

    public function __construct($guestName, $bookingNumber, $roomNumber, $date) {
        $this->guestName = $guestName;
        $this->bookingNumber = $bookingNumber;
        $this->roomNumber = $roomNumber;
        $this->date = $date;
    }

    public function render() {
        echo '<div class="card" style="background-color: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">';
        echo '<div>';
        echo '<h2 style="margin-top: 0; margin-bottom: 5px;">' . htmlspecialchars($this->guestName) . '</h2>';
        echo '<p style="margin-bottom: 0;">Booking #' . htmlspecialchars($this->bookingNumber) . ' | Room ' . htmlspecialchars($this->roomNumber) . ' | ' . htmlspecialchars($this->date) . '</p>';
        echo '</div>';
        echo '<div>';
        echo '<button class="btn primary" style="background-color: #007bff; color: white; border: none; padding: 8px 12px; margin-left: 5px; border-radius: 5px; cursor: pointer;">CONFIRM</button>';
        echo '<button class="btn warning" style="background-color: #ffc107; color: #212529; border: none; padding: 8px 12px; margin-left: 5px; border-radius: 5px; cursor: pointer;">MODIFY</button>';
        echo '<button class="btn danger" style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; margin-left: 5px; border-radius: 5px; cursor: pointer;">CANCEL</button>';
        echo '</div>';
        echo '</div>';
    }

     protected static function table($id){
        for($x = 0; $x < $id; $x++){
            $this->render();
        }
    }
}

?>