<body class="bg" style="background-image:url(../image/bg.png); position:fixed;">
    <div class="flex">

        <aside id="navbar" class=" text-blue-800 w-64" style="font-size: 20px; background-color: rgba(75, 216, 226, 0.75);">

            <nav class="space-y-2 mt-16 mb-10 p-4" style="color:white; ">
            <div class="logo text-xxl font-semibold text-white-800 flex items-center mb-5 ml-2 mt-4" style="font-size:40px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="white" class="bi bi-house-fill" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                            <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                            </svg>
                            HBMS
                        </div>
                        <a href="../Main/index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Dashboard</a>
                        <a href="../Rooms/index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Room Management</a>
                        <a href="../Amenities/index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Amenities</a>
                        <a href="../Reservation/index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Reservations</a>
                        <a href="../Reservation/checkin_in_out.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Check-in/Check-out</a>
                        <a href="../Review/index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Guest Feedbacks</a>
                        <a href="index.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Manage Users</a>
                        <a href="../auth/logout.php" class="block px-4 py-2 rounded hover:bg-teal-200 transition-colors">Log Out</a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <header>
                <div class="flex">
                <div class="menu-container mr-4">
                                <button id="menu-button" class="bg-white-500 text-black m flex items-center gap-2" >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                                    </svg>
                                    MENU
                                </button>
                            </div>
                            </div>

                            <div class="logo text-xl font-semibold text-gray-800 flex items-center" style="margin-left: auto; position:relative; text-align:middle;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                                <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                                </svg>
                                HBMS
                            </div>

                            <div class = "profile mr-4">
                                <div class="text-gray-600 mr-4">Super</div>
                                <img src="https://placehold.co/40x40/80ED99/fff?text=U&font=Montserrat" alt="User Avatar" class="rounded-full">
                            </div>
                        </header>



<script>
        const menuContainer = document.querySelector('.menu-container');
        const navbar = document.querySelector('#navbar');
        const mainContent = document.querySelector('main');
        let isNavbarVisible = false;

        menuContainer.addEventListener('mouseenter', () => {
            navbar.classList.add('show');
            mainContent.classList.add('shifted');
            isNavbarVisible = true;
        });

        navbar.addEventListener('mouseleave', () => {
            navbar.classList.remove('show');
            mainContent.classList.remove('shifted');
            isNavbarVisible = false;
        });

        navbar.addEventListener('mouseenter', () => {
            isNavbarVisible = true;
        })
</script>                        