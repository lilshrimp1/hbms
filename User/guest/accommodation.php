<?php 

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HBMS Room Services</title>
  <!-- Bootstrap CDN -->
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
      width: 200 ;
      height: 200px;
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
    }

    .room-container {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#" style="margin-left: 200px;">HBMS</a>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item" style="margin-right:100px;"><a class="nav-link fw-semibold" href="#">Home</a></li>
        <li class="nav-item" style="margin-right:100px;"><a class="nav-link fw-semibold" href="#">Accommodation</a></li>
        <li class="nav-item" style="margin-right:450px;"><a class="nav-link fw-semibold" href="#">Manage Profile</a></li>
        <li class="nav-item">
          <a class="nav-link" href="#" style="margin-right: 100px;"><i class="bi bi-person-circle"></i> Moses Alfonso</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Header -->
<header>
  <h1>Room services</h1>
</header>

<!-- Room Cards -->
<div class="room-container" id="roomContainer">
  <div class="room-card"  onclick="scrollToCenter(this)">
    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c" alt="Single Bedroom" />
    <div class="card-body text-center">
      <h5 class="fw-bold mt-2">Single Bedroom</h5>
      <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal"
         onclick="openRoomModal('Single Bedroom', 'A cozy room for one with all basic amenities.', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c')">Details</a><br>
      <button class="btn btn-info text-white rounded-pill mt-2">Add room +</button>
    </div>
  </div>

  <div class="room-card" onclick="scrollToCenter(this)">
    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2" alt="Two Bedroom" />
    <div class="card-body text-center">
      <h5 class="fw-bold mt-2">Two Bedroom</h5>
      <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal"
         onclick="openRoomModal('Two Bedroom', 'Spacious room with two beds and a mini-living space.', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2')">Details</a><br>
      <button class="btn btn-info text-white rounded-pill mt-2">Add room +</button>
    </div>
  </div>

  <div class="room-card" onclick="scrollToCenter(this)">
    <img src="https://images.unsplash.com/photo-1600585152915-d208bec867a9" alt="Family Bedroom" />
    <div class="card-body text-center">
      <h5 class="fw-bold mt-2">Family Bedroom</h5>
      <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal"
         onclick="openRoomModal('Family Bedroom', 'Ideal for families with children. Comes with extra bedding and space.', 'https://images.unsplash.com/photo-1600585152915-d208bec867a9')">Details</a><br>
      <button class="btn btn-info text-white rounded-pill mt-2">Add room +</button>
    </div>
  </div>

  <div class="room-card" onclick="scrollToCenter(this)">
    <img src="https://images.unsplash.com/photo-1600585152915-d208bec867a9" alt="Deluxe Bedroom" />
    <div class="card-body text-center">
      <h5 class="fw-bold mt-2">Deluxe Bedroom</h5>
      <a href="#" data-bs-toggle="modal" data-bs-target="#roomModal"
         onclick="openRoomModal('Deluxe Bedroom', 'Premium luxury with elegant design and full amenities.', 'https://images.unsplash.com/photo-1600585152915-d208bec867a9')">Details</a><br>
      <button class="btn btn-info text-white rounded-pill mt-2">Add room +</button>
    </div>
  </div>
</div>

<!-- Room Info Modal -->
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

<!-- Scroll-to-Center Script -->
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'?>