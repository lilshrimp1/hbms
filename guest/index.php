<?php
include 'header.php';


$db = new database();
$conn = $db->getConnection();
$modals = new Modals(); // Instantiate the Modals class

// Fetch available rooms
$sqlAvailableRooms = "SELECT r.id as room_id, r.room_number, rt.name as room_type_name, r.description, r.price, r.status, r.capacity
                      FROM room r
                      JOIN room_types rt ON r.type_id = rt.id
                      WHERE r.status = 'available'";
$stmtAvailableRooms = $conn->prepare($sqlAvailableRooms);
$stmtAvailableRooms->execute();
$allAvailableRooms = $stmtAvailableRooms->fetchAll();
$totalAvailableRooms = count($allAvailableRooms);
$initialDisplayCount = min(3, $totalAvailableRooms);
$displayedRooms = array_slice($allAvailableRooms, 0, $initialDisplayCount);

// Fetch booking history (same as before)
$sqlBookingHistoryList = "SELECT res.id as reservation_id,
                                 rt.name as room_type_name,
                                 r.room_number,
                                 res.check_in,
                                 res.check_out
                           FROM reservations res
                           JOIN room r ON res.room_id = r.id
                           JOIN room_types rt ON r.type_id = rt.id
                           ORDER BY res.check_in DESC";
$stmtBookingHistoryList = $conn->prepare($sqlBookingHistoryList);
$stmtBookingHistoryList->execute();
$allBookingHistoryList = $stmtBookingHistoryList->fetchAll();

// Fetch all booking history details for the modal (same as before)
$sqlBookingHistoryDetails = "SELECT res.id as reservation_id,
                                     rt.name as room_type_name,
                                     r.room_number,
                                     res.check_in,
                                     res.check_out,
                                     res.guests,
                                     res.status as reservation_status
                               FROM reservations res
                               JOIN room r ON res.room_id = r.id
                               JOIN room_types rt ON r.type_id = rt.id";
$stmtBookingHistoryDetails = $conn->prepare($sqlBookingHistoryDetails);
$stmtBookingHistoryDetails->execute();
$allBookingHistoryDetails = $stmtBookingHistoryDetails->fetchAll();

// Create an associative array for quick lookup of booking details
$bookingDetailsLookup = [];
foreach ($allBookingHistoryDetails as $detail) {
    $bookingDetailsLookup[$detail['reservation_id']] = $detail;
}


?>

<div class="textcenter">
    <div class="overlay"></div>
    <h1 class="welcome">WELCOME</h1>
    <h2 class="subtext">LET'S BOOK NOW</h2>
</div>

<div class="dashboard-container">
    <div class="dashboard-card">
        <h3 style="margin-left: 30px;">Upcoming Booking</h3>
        <div class="upcoming-booking-wrapper">
            <button class="navigation-button left-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-left-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1h-5.793l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </button>
            <div class="upcoming-booking-visual">
            <div class="upcoming-booking-image-container">
                <img src="../images/single_bedroom.jpeg" alt="Upcoming Booking Room">
                </div>
                <div class="upcoming-booking-details">
                    <h2>Two Bedroom</h2>
                        <p>Room ID: 101</p>
                        <p>Check-in Date: 05/05/2025</p>
                        <p>Check-out Date: 07/05/2025</p>
                        <p>Amenity: TV, AC</p>
                        <p>Number of Guests: 2</p>
                        <p>Status: Confirmed</p>
                </div>
            </div>
            <button class="navigation-button right-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
                </svg>
            </button>
            <div class="dashboard-summary">
                <div>
                    <p>Total of Upcoming Booking:</p>
                    <span class="number">2</span>
                </div>
                <div>
                    <p>Total Booking:</p>
                    <span class="number">12</span>
                </div>
                <br>
                <div>
                    <p>Total of Available Rooms:</p>
                    <span class="number">22</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="lastpage">
    <div class="available-rooms-section" style="margin-left: 100px; margin-top: 20px;">
        <h3>Available rooms</h3>
        <div class="room-container" id="available-rooms-container">
            <?php foreach ($displayedRooms as $room): ?>
                <div class="room-card">
                    <?php
                      $imagePath = ImagePaths::getRoomTypeImage($room['room_type_name']);
                      if (!empty($imagePath)):
                    ?>
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $room['room_type_name']; ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5><?php echo $room['room_type_name']; ?> (Room <?php echo $room['room_number']; ?>)</h5>
                        <p><?php echo $room['description']; ?></p>
                        <button class="btn btn-info view-details-btn" data-room-id="<?php echo $room['room_id']; ?>" data-toggle="modal" data-target="#roomDetailsModal">View Details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top: 20px; margin-left: 40px;">
            Available rooms: <span style="font-weight: bold;"><?php echo $totalAvailableRooms; ?></span>
        </div>
        <?php if ($totalAvailableRooms > 3): ?>
            <div class="navigation-arrows" style="margin-top: 10px; text-align: center;">
                <button id="prev-room" style="padding: 5px 10px; margin: 0 10px;">&lt;</button>
                <button id="next-room" style="padding: 5px 10px; margin: 0 10px;">&gt;</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="booking-history-section" style="margin-top: 20px; margin-bottom: 20px; margin-left: 200px; display: inline-block; vertical-align: top;">
        <h3>Booking history</h3>
        <div class="booking-search">
            <input type="text" id="booking-search-input" placeholder="Date/Room">
            <button id="booking-search-button">&#128269;</button>
        </div>
        <ul class="booking-list" id="booking-history-list">
            <?php if (!empty($allBookingHistoryList)): ?>
                <?php foreach ($allBookingHistoryList as $booking): ?>
                    <li class="booking-item" data-reservation-id="<?php echo $booking['reservation_id']; ?>" style="cursor: pointer;">
                        <?php echo $booking['room_type_name']; ?> (Room <?php echo $booking['room_number']; ?>)
                        <span><?php echo date('m/d/Y', strtotime($booking['check_in'])); ?></span>
                        -
                        <span><?php echo date('m/d/Y', strtotime($booking['check_out'])); ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No booking history available.</li>
            <?php endif; ?>
        </ul>
    </div>

    <?php
    // Modal for displaying room details
    echo $modals::layout('roomDetails', 'Room Details', ['<div id="modal-room-details"></div>']);
    ?>

</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="accommodation.php" style="text-decoration: none;">
        <button class="btn btn-success btn-lg" style="background-color: #00cfff; border: none; cursor: pointer;">BOOK NOW!</button>
    </a>
</div>

<script>
    // Make the PHP arrays available to JavaScript
    const bookingDetailsLookupJS = <?php echo json_encode($bookingDetailsLookup); ?>;
    const allBookingHistoryListJS = <?php echo json_encode($allBookingHistoryList); ?>;
    const allRooms = <?php echo json_encode($allAvailableRooms); ?>; // Make allRooms available

    const roomsContainer = document.getElementById('available-rooms-container');
    const prevButton = document.getElementById('prev-room');
    const nextButton = document.getElementById('next-room');
    const roomsPerPage = 3;
    let currentPage = 0;

    function displayRooms(page) {
        roomsContainer.innerHTML = '';
        const startIndex = page * roomsPerPage;
        const endIndex = startIndex + roomsPerPage;
        const currentRooms = allRooms.slice(startIndex, endIndex);

        if (currentRooms.length === 0 && page > 0) {
            currentPage--;
            displayRooms(currentPage);
            return;
        }

        currentRooms.forEach(room => {
            const roomCard = document.createElement('div');
            roomCard.classList.add('room-card');
            const imagePath = `<?php echo ImagePaths::getRoomTypeImage('${room.room_type_name}'); ?>`;
            const imageTag = imagePath ? `<img src="${imagePath}" alt="${room.room_type_name}">` : '';
            roomCard.innerHTML = `
                ${imageTag}
                <div class="card-body">
                    <h5>${room.room_type_name} (Room ${room.room_number})</h5>
                    <p>${room.description}</p>
                    <button class="btn btn-info view-details-btn" data-room-id="${room.room_id}" data-toggle="modal" data-target="#roomDetailsModal">View Details</button>
                </div>
            `;
            roomsContainer.appendChild(roomCard);
        });

        prevButton.disabled = currentPage === 0;
        nextButton.disabled = (currentPage + 1) * roomsPerPage >= allRooms.length;
    }

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            currentPage--;
            displayRooms(currentPage);
        });

        nextButton.addEventListener('click', () => {
            currentPage++;
            displayRooms(currentPage);
        });

        displayRooms(0);
    }

    const bookingSearchInput = document.getElementById('booking-search-input');
    const bookingSearchButton = document.getElementById('booking-search-button');
    const bookingHistoryList = document.getElementById('booking-history-list');

    function filterBookingHistory() {
        const searchTerm = bookingSearchInput.value.toLowerCase();
        bookingHistoryList.innerHTML = '';

        const filteredHistory = allBookingHistoryListJS.filter(booking => {
            const roomTypeName = booking.room_type_name.toLowerCase();
            const roomNumber = booking.room_number.toLowerCase();
            const checkInDate = new Date(booking.check_in).toLocaleDateString().toLowerCase();
            const checkOutDate = new Date(booking.check_out).toLocaleDateString().toLowerCase();
            return (
                roomTypeName.includes(searchTerm) ||
                roomNumber.includes(searchTerm) ||
                checkInDate.includes(searchTerm) ||
                checkOutDate.includes(searchTerm)
            );
        });

        if (filteredHistory.length > 0) {
            filteredHistory.forEach(booking => {
                const listItem = document.createElement('li');
                listItem.classList.add('booking-item');
                listItem.dataset.reservationId = booking.reservation_id;
                listItem.style.cursor = 'pointer';
                listItem.innerHTML = `
                    ${booking.room_type_name} (Room ${booking.room_number})
                    <span>${new Date(booking.check_in).toLocaleDateString()}</span>
                    -
                    <span>${new Date(booking.check_out).toLocaleDateString()}</span>
                `;
                bookingHistoryList.appendChild(listItem);
            });
        } else {
            const listItem = document.createElement('li');
            listItem.textContent = 'No matching booking history found.';
            bookingHistoryList.appendChild(listItem);
        }
        attachBookingItemListeners(); // Re-attach listeners after filtering
    }

    bookingSearchButton.addEventListener('click', filterBookingHistory);
    bookingSearchInput.addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            filterBookingHistory();
        }
    });

    // JavaScript to handle the room details modal
    const roomDetailsModal = document.getElementById('roomDetailsModal');
    const modalRoomDetails = document.getElementById('modal-room-details');

    const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.dataset.roomId;
            fetchRoomDetails(roomId);
        });
    });

    async function fetchRoomDetails(roomId) {
        try {
            const response = await fetch(`get_room_details.php?id=${roomId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            displayRoomDetailsInModal(data);
        } catch (error) {
            console.error("Could not fetch room details:", error);
            modalRoomDetails.innerHTML = '<p>Error loading details.</p>';
        }
    }

    function displayRoomDetailsInModal(details) {
        modalRoomDetails.innerHTML = `
            <h3>${details.room_type_name} (Room ${details.room_number})</h3>
            <p>Description: ${details.description}</p>
            <p>Price: $${details.price}</p>
            <p>Capacity: ${details.capacity} guests</p>
            <p>Status: ${details.status}</p>
            `;
        // Bootstrap will handle showing the modal because of the data-toggle and data-target attributes on the button.
    }
</script>

<?php include 'footer.php'; ?>