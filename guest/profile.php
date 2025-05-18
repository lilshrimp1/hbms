<?php
include 'header.php';


$database = new database();
$conn = $database->getConnection();
User::setConnection($conn);
$user = User::find($_SESSION['user_id']);
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 rounded" >
        <div class="row align-items-center mb-2" >
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-light border p-5" style="width: 250px; height: 250px; margin-top:50px; margin-right:50px; ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="170" height="170" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    </svg>
                </div>
            </div>
            <div class="col-md-8" style="margin-top:30px; padding:10px; margin-right:-120px; margin-left:80px; font-size: 20px;">
                <h4><?php echo $user->name; ?></h4>
                <p class="text-muted mb-0"><?php echo $user->email; ?></p>
                <div class="mb-2">
                    <p class="mb-0"><strong class="text-muted">Contact Number:</strong> <?php echo $user->contact_no; ?></p>
                </div>
                <div>
                    <p class="mb-0"><strong class="text-muted">Address:</strong> <?php echo $user->address; ?></p>
                </div>
            </div>
        </div>

        <div class="d-grid mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">Edit Profile</button>
        </div>

        <?php echo Modals::layout('update', 'update'); ?>

    </div>
</div>

<?php
include 'footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>