<?php
include 'header.php';

$database = new database();
$conn = $database->getConnection();

Reservation::setConnection($conn);
RoomType::setConnection($conn);
Room::setConnection($conn);
$reservations = Reservation::where('user_id', '=', $_SESSION['user_id']);
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HBMS Room Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        header {
            background:
                linear-gradient(to bottom, rgba(255, 255, 255, 0) 70%, rgb(255, 255, 255) 100%),
                url('https://images.unsplash.com/photo-1505691938895-1758d7feb511') no-repeat center center;
            background-size: cover;
            color: white;
            text-align: center;
            padding: 100px 20px 60px;
        }

        header h1 {
            font-size: 4rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .room-container {
            margin-left: 100px;
            margin-top: -100px;
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            gap: 20px;
            padding: 40px;
            scroll-behavior: smooth;
        }

        .room-card {
            flex: 0 0 auto;
            scroll-snap-align: center;
            width: 400px;
            height: 500px;
            border-radius: 20px;
            background-color: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, z-index 0.3s;
            cursor: pointer;
        }

        .room-card:hover {
            transform: scale(1.1);
            z-index: 10;
        }

        .room-card img {
            margin-top: 20px;
            margin-left: 50px;
            width: 300px;
            height: 300px;
            object-fit: cover;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-body {
            padding: 15px;
        }

        .btn-info {
            background-color: #00cfff;
            border: none;
        }

        .btn-info:hover {
            background-color: #00b5dd;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        /* Hide scrollbar */
        .room-container::-webkit-scrollbar {
            display: none;
            opacity: 20%;
        }

        .room-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .reservation-table-container {
            
            padding: 100px;
        }

        .reservation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .reservation-table th, .reservation-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        .reservation-table th {
            background-color: #f8f9fa;
        }

        .action-icons {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .action-icons button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .action-icons img {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>

<header>
    <h1>Room services</h1>
</header>

<div class="room-container" id="roomContainer">
    <div class="room-card" onclick="scrollToCenter(this)">
        <img src="../images/single_bedroom.jpeg" alt="Single Bedroom" />
        <div class="card-body text-center">
            <h5 class="fw-bold mt-2">Single Bedroom</h5>
            <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal" style="text-decoration: none;"
               onclick="openRoomModal('Single Bedroom', 'A cozy room for one with all basic amenities.', '../images/single_bedroom.jpeg')">Details</a><br>
            <button class="btn btn-info text-white rounded-pill mt-2"
                    data-bs-toggle="modal" data-bs-target="#bookRoomModal">Add room +</button>
        </div>
    </div>

    <div class="room-card" onclick="scrollToCenter(this)">
        <img src="../images/two_bedroom.png" alt="Two Bedroom" />
        <div class="card-body text-center">
            <h5 class="fw-bold mt-2">Two Bedroom</h5>
            <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal" style="text-decoration: none;"
               onclick="openRoomModal('Two Bedroom', 'Spacious room with two beds and a mini-living space.', '../images/two_bedroom.png')">Details</a><br>
            <button class="btn btn-info text-white rounded-pill mt-2"
                    data-bs-toggle="modal" data-bs-target="#bookRoomModal">Add room +</button>
        </div>
    </div>

    <div class="room-card" onclick="scrollToCenter(this)">
        <img src="../images/family_bedroom.png" alt="Family Bedroom" />
        <div class="card-body text-center">
            <h5 class="fw-bold mt-2">Family Bedroom</h5>
            <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal" style="text-decoration: none;"
               onclick="openRoomModal('Family Bedroom', 'Ideal for families with children. Comes with extra bedding and space.', '../images/family_bedroom.png')">Details</a><br>
            <button class="btn btn-info text-white rounded-pill mt-2"
                    data-bs-toggle="modal" data-bs-target="#bookRoomModal">Add room +</button>
        </div>
    </div>

    <div class="room-card" onclick="scrollToCenter(this)">
        <img src="../images/suite.png" alt="Deluxe Bedroom" />
        <div class="card-body text-center">
            <h5 class="fw-bold mt-2">Deluxe Bedroom</h5>
            <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal" style="text-decoration: none;"
               onclick="openRoomModal('Deluxe Bedroom', 'Premium luxury with elegant design and full amenities.', '../images/suite.png')">Details</a><br>
            <button class="btn btn-info text-white rounded-pill mt-2"
                    data-bs-toggle="modal" data-bs-target="#bookRoomModal">Add room +</button>
        </div>
    </div>
</div>

<?php if ($reservations): ?>
<div class="reservation-table-container">
    <h1>SUMMARY</h1>
    <table class="reservation-table">
        <thead>
            <tr>
                <th>Room ID</th>
                <th>Room Type</th>
                <th>Date of Reservation</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($reservations as $reservation): 
                $rooms = Room::find($reservation->room_id);
                $room_types = RoomType::find($rooms->type_id);

                // Calculate total due
                $checkInDate = new DateTime($reservation->check_in);
                $checkOutDate = new DateTime($reservation->check_out);
                $interval = $checkInDate->diff($checkOutDate);
                $nights = $interval->days;
                $totalDue = $rooms ? $rooms->price * $nights : 0;
            ?>
            <tr>
                <td><?php echo $reservation->room_id; ?></td>
                <td><?php echo $room_types->name; ?></td>
                <td><?php echo $reservation->check_in; ?></td>
                <td><?php echo $reservation->status; ?></td>
                <td class="action-icons">
                    <button type="button" class="btn btn-sm btn-primary view-reservation-btn" data-bs-toggle="modal" data-bs-target="#viewReservationModal" data-reservation-id="<?php echo $reservation->id; ?>" data-total-due="<?php echo $totalDue; ?>" title="View Reservation" aria-label="View Reservation" style="color: black; font-size: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-three-dots" viewBox="0 0 16 16">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"/>
                        </svg>
                    </button>
                    <button type="button" class="btn btn-sm btn-warning edit-reservation-btn" data-bs-toggle="modal" data-bs-target="#editReservationModal" data-reservation-id="<?php echo $reservation->id; ?>" title="Edit Reservation" aria-label="Edit Reservation" style="color: black; font-size: 1rem; margin-left: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                        </svg>
                    </button>
<form action="../Reservation/cancel.php" method="POST" style="display:inline; margin-left: 8px;" onsubmit="return confirm('Are you sure you want to cancel this reservation?');" aria-label="Cancel Reservation">
    <input type="hidden" name="reservation_id" value="<?php echo $reservation->id; ?>" />
    <button type="submit" class="btn btn-sm btn-danger" style="color: black; font-size: 1rem; background: none; border: none; padding: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-x-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
        </svg>
    </button>
</form>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>

<?php endif; ?>

<?php
// Render the viewReservation modal
echo Modals::layout('viewReservation');
// Render the editReservation modal
echo Modals::layout('editReservation');
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var viewButtons = document.querySelectorAll('.view-reservation-btn');
    var modalElement = document.getElementById('viewReservationModal');
    var modalBody = modalElement.querySelector('.modal-body');

    viewButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var reservationId = this.getAttribute('data-reservation-id');

            // Clear previous content
            modalBody.innerHTML = '<h2 class="text-center mb-3" style="font-family: Cal Sans, sans-serif; font-weight: 700; font-size: 1.5rem;">Loading...</h2>';

            // Fetch reservation details via AJAX
            fetch('get_reservation_details.php?id=' + reservationId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        modalBody.innerHTML = '<p>Error loading reservation details.</p>';
                    } else {
                        // Use the modal layout with data to render content
                        fetch('render_modal_content.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ modal: 'viewReservation', data: data })
                        })
                        .then(response => response.text())
                        .then(html => {
                            modalBody.innerHTML = html;
                        })
                        .catch(() => {
                            modalBody.innerHTML = '<p>Error rendering modal content.</p>';
                        });
                    }
                })
                .catch(() => {
                    modalBody.innerHTML = '<p>Error loading reservation details.</p>';
                });
        });
    });

    // Edit reservation modal logic
    var editButtons = document.querySelectorAll('.edit-reservation-btn');
    var editModalElement = document.getElementById('editReservationModal');
    var editModalBody = editModalElement.querySelector('.modal-body');

    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var reservationId = this.getAttribute('data-reservation-id');

            // Clear previous content
            editModalBody.innerHTML = '<h2 class="text-center mb-3" style="font-family: Cal Sans, sans-serif; font-weight: 700; font-size: 1.5rem;">Loading...</h2>';

            // Fetch reservation details via AJAX
            fetch('get_reservation_details.php?id=' + reservationId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        editModalBody.innerHTML = '<p>Error loading reservation details.</p>';
                    } else {
                        // Use the modal layout with data to render edit form
                        fetch('render_modal_content.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ modal: 'editReservation', data: data })
                        })
                        .then(response => response.text())
                        .then(html => {
                            editModalBody.innerHTML = html;
                        })
                        .catch(() => {
                            editModalBody.innerHTML = '<p>Error rendering edit modal content.</p>';
                        });
                    }
                })
                .catch(() => {
                    editModalBody.innerHTML = '<p>Error loading reservation details.</p>';
                });
        });
    });
});
</script>

<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalLabel">Room Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalRoomImage" src="" class="img-fluid rounded mb-3" alt="Room Image">
                <p id="modalRoomDescription" class="text-muted"></p>
            </div>
        </div>
    </div>
</div>


<?php
    // This prints the create->book modal with id "createModal"
    echo Modals::layout('bookRoom');
?>

<script>
    function scrollToCenter(card) {
        const container = document.getElementById('roomContainer');
        const containerRect = container.getBoundingClientRect();
        const cardRect = card.getBoundingClientRect();
        const scrollLeft = container.scrollLeft;
        const offset = (cardRect.left + cardRect.width / 2) - (containerRect.left + containerRect.width / 2);

        container.scrollTo({
            left: scrollLeft + offset,
            behavior: 'smooth'
        });
    }

    function openRoomModal(title, description, imageUrl) {
        document.getElementById('roomModalLabel').textContent = title;
        document.getElementById('modalRoomDescription').textContent = description;
        document.getElementById('modalRoomImage').src = imageUrl;
    }

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'?>
