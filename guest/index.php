<?php
include 'header.php';
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
        <div class="room-container">
            <div class="room-card">
                <img src="/HOTEL-BOOKING-MANAGEMENT-SYSTEM/photos/single_bedroom.jpeg" alt="Single Room">
                <div class="card-body">
                    <h5>Single Bedroom</h5>
                    <p>Details about the single bedroom.</p>
                    <a href="#" class="btn btn-info">View Details</a>
                </div>
            </div>
            <div class="room-card">
                <img src="/HOTEL-BOOKING-MANAGEMENT-SYSTEM/photos/two_bedroom.png" alt="Two Bedroom">
                <div class="card-body">
                    <h5>Two Bedroom</h5>
                    <p>Details about the two bedroom.</p>
                    <a href="#" class="btn btn-info">View Details</a>
                </div>
            </div>
            <div class="room-card">
                <img src="/HOTEL-BOOKING-MANAGEMENT-SYSTEM/photos/suite.png" alt="Luxery Room">
                <div class="card-body">
                    <h5>Luxery</h5>
                    <p>Details about the Luxery room.</p>
                    <a href="" class="btn btn-info">View Details</a>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px; margin-left: 40px;">
            Available rooms: <span style="font-weight: bold;">3</span>
        </div>
    </div>

    <div class="booking-history-section" style="margin-top: 20px; margin-bottom:20px; margin-left: 200px; display: inline-block; vertical-align: top;">
        <h3>Booking history</h3>
        <div class="booking-search">
            <input type="text" placeholder="Date/Room">
            <button>&#128269;</button>
        </div>
        <ul class="booking-list">
            <li>Single bedroom <span>10/19/2023</span> <span>...</span></li>
            <li>Two bedroom <span>10/11/2023</span> <span>...</span></li>
            </ul>
    </div>
</div>

<div style="text-align: center; margin-top: 30px;">
    <a href="accommodation.php" style="text-decoration: none;">
        <button class="btn btn-success btn-lg" style="background-color: #00cfff; border: none; cursor: pointer;">BOOK NOW!</button>
    </a>
</div>


<?php include 'footer.php'?>
