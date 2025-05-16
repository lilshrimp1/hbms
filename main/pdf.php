<?php session_start(); ?>
<?php include '../database.php'; ?>
<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>


<div class="container-xxl mt-5">
    <form action="../reports/books_report.php" method="POST">
    <div class="card shadow mb-3">
            <div class="card-header bg-danger-subtle text-white">
                    <h2 class="text-left px-3">PDF Books Report Generator</h2>
            </div>
        <div class="card-body">
                <button type="submit" class="btn btn-light btn-lg" name="report" value="all_books">Generate All Books Report</button>
                <br><br>
                <div class="col-md-3">
                <label for="year">Year Published:</label>
                <input type="number" id="year" name="year" class="form-control mb-3" placeholder="Enter year">
                <button type="submit" class="btn btn-light btn-lg" name="report" value="books_by_year">Generate Books by Year Published</button>
                </div>            
            </div> 
        </div>
    </form>
</div>
<?php if(isset($_SESSION['role']) && ($_SESSION['role'] != 'Librarian' && $_SESSION['role'] != 'Admin')){ ?>
<div class="container-xxl mt-5">
    <form action="../reports/users_report.php" method="POST">
    <div class="card shadow mb-3">
            <div class="card-header bg-danger-subtle text-white">
                    <h2 class="text-left px-3">PDF Users Report Generator</h2>
            </div>
        <div class="card-body mb-3">
            <button type="submit" class="btn btn-light btn-lg" name="report" value="all_users">Generate All Users Report</button>
            <br><br>
            <button type="submit" class="btn btn-light btn-lg" name="report" value="active_users">Generate Active Users Report</button>
            <br><br>
            <button type="submit" class="btn btn-light btn-lg" name="report" value="inactive_users">Generate Inactive Users Report</button>
            </div> 
        </div>
    </form>
</div>
<?php } ?>



<?php include '../layout/footer.php'; ?>