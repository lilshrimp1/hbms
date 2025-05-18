const open = document.getElementById('open');
const modal_container = document.getElementById('modal_container'); // Corrected the ID
const close = document.getElementById('close');

open.addEventListener('click', () => {
  modal_container.classList.add('show');
});

close.addEventListener('click', () => {
  modal_container.classList.remove('show');
});

(function() {
    const modal_container = document.getElementById('bookingDetailsModal');
    const close = modal_container ? modal_container.querySelector('.close-button') : null;

    function showBookingDetails(reservationId) {
        const details = bookingDetailsLookupJS[reservationId]; // Access global variable

        const modalContentDiv = modal_container ? modal_container.querySelector('.modal-body') : null;

        if (details && modalContentDiv && modal_container) {
            const modalContent = `
                <h2 class='text-center mb-3' style='font-family: Cal Sans, sans-serif; font-weight: 700; font-size: 1.5rem;'>Booking Details</h2>
                <div class='booking-details'>
                    <p>Room Type: <span>${details.room_type_name}</span></p>
                    <p>Room Number: <span>${details.room_number}</span></p>
                    <p>Check-in Date: <span>${new Date(details.check_in).toLocaleDateString()}</span></p>
                    <p>Check-out Date: <span>${new Date(details.check_out).toLocaleDateString()}</span></p>
                    <p>Number of Guests: <span>${details.guests}</span></p>
                    <p>Status: <span>${details.reservation_status}</span></p>
                </div>
                <style> /* Basic styling, adjust as needed */
                    .booking-details p { margin-bottom: 5px; }
                    .booking-details span { font-weight: bold; }
                </style>
            `;
            modalContentDiv.innerHTML = modalContent;
            modal_container.classList.add('show');
        }
    }

    function attachBookingItemListeners() {
        const bookingHistoryList = document.getElementById('booking-history-list');
        if (bookingHistoryList) {
            const bookingItems = bookingHistoryList.querySelectorAll('.booking-item');
            bookingItems.forEach(item => {
                item.addEventListener('click', function() {
                    const reservationId = this.dataset.reservationId;
                    showBookingDetails(reservationId);
                });
            });
        }
    }

    if (close) {
        close.addEventListener('click', () => {
            modal_container.classList.remove('show');
        });
    }

    window.addEventListener('click', (event) => {
        if (modal_container && event.target === modal_container) {
            modal_container.classList.remove('show');
        }
    });

    // Make sure to call this function from index.php *after* the list is generated
    window.attachBookingItemListeners = attachBookingItemListeners;
})();