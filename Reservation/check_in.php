<?php session_start();?>
<?php include '../layout/header.php'; ?>
<?php include '../auth/super.php'; ?>
<?php require_once '../Database/database.php'; 
      require_once '../models/Reservation.php';
      require_once '../models/Room.php'; 
      require_once '../models/Payment.php';

        $database = new database();
        $conn = $database->getConnection();
?>

<?php
Reservation::setConnection($conn);
// If deletion is confirmed via POST
if (isset($_POST['confirm_delete'])) {
    $id = $_GET['id'];
    $reservation = Reservation::find($id);
    $room = Room::find($reservation->room_id);
    $payments = Payment::where('reservation_id', '=', $reservation->id);
    $payment = is_array($payments) && count($payments) > 0 ? $payments[0] : null;
    $reservation->check_in = date('Y-m-d H:i:s');
    $reservation->status = 'Checked In';
    $room->status = 'Occupied';
    $payment->status = 'Pending';


    if (!$reservation->save()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Checked In!",
                    text: "The reservation has been checked in.",
                    icon: "success"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Failed to Check In Reservation, please try again!",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then(() => {
                    window.location = "index.php";
                });
            });
        </script>';
    }
} else {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Are you sure?",
                text: "You won\'t be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, deactivate it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement("form");
                    form.method = "POST";
                    form.action = window.location.href;
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "confirm_delete";
                    input.value = "1";
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    window.location = "index.php";
                }
            });
        });
    </script>';
}
?>

<?php include '../layout/footer.php'; ?>