
    <div class="toggle-btn mt-2" onclick="toggleSidebar()" style="font-size: 30px">
    <span class="icon"><i class="fa fa-bars"></i></span>
    <span class="text" style="font-family: 'Shadows Into Light', cursive;">Menu</span>
</div>

<div class="sidebar" id="sidebar">
  <ul>
    <li class="logo"><a href="../main/index.php">
        <span class="icon"><i class="fa fa-clipboard-user"></i></span>
        <span class="text">Dashboard</span></a>
    </li>
    <li><a href="../Rooms/index.php">
        <span class="icon"><i class="fa fa-book"></i></span>
        <span class="text">Rooms</span></a>
    </li>
    <?php if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Librarian' && $_SESSION['role'] != 'Admin')){ ?>
    <li><a href="../Amenities/index.php">
        <span class="icon"><i class="fa fa-user"></i></span>
        <span class="text">Amenities</span></a>
    </li>
    <?php } ?>
    <li><a href="../main/pdf.php">
        <span class="icon"><i class="fa fa-file-pdf"></i></span>
        <span class="text">PDF</span></a>
    </li>
    <li><a href="../auth/logout.php">
        <span class="icon"><i class="fa fa-sign-out"></i></span>
        <span class="text">Logout</span></a>
    </li>
  </ul>
</div>

<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
  }
</script>