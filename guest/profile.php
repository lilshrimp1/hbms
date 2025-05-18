<?php
include 'header.php';
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 rounded">
        <div class="row align-items-center mb-3">
            <div class="col-md-4 text-center">
                <div class="rounded-circle bg-light border p-5" style="width: 320px; height: 320px; margin-top:70px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    </svg>
                </div>
            </div>
            <div class="col-md-8" style="margin-top:-50px;">
                <h4>Moses Alfonso</h4>
                <p class="text-muted mb-0">mosesjamelalfonso@gmail.com</p>
                <div class="mb-2">
                    <p class="mb-0"><strong class="text-muted">Contact Number:</strong> 09196795916</p>
                </div>
                <div>
                    <p class="mb-0"><strong class="text-muted">Address:</strong> Brgy. Tondod, San Jose City</p>
                </div>
            </div>
        </div>

        <div class="d-grid mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">Edit Profile</button>
        </div>

        <?php echo $modals->layout('update', 'update'); ?>

    </div>
</div>

<?php
include 'footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>