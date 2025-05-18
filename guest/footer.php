</body>

<footer style="display: flex; justify-content: space-between; align-items: center; padding: 100px 20px 60px; background-image: url('../images/footer.jpg'), linear-gradient(to top, rgba(0, 0, 0, 1), rgb(245, 240, 240)); background-blend-mode: overlay; background-repeat: no-repeat; background-position: center center; background-size: cover; background-color: transparent; width: 100%; height: auto;">
    <div style="color: white;">
        <h2 style="font-size: 50px; text-shadow: 2px 2px 5px #000000;">HBMS</h2>
        <p>Information about the system or the hotel</p>
    </div>

    <div style="color: white; text-align: right;">
        <div style="display: flex; align-items: center; justify-content: flex-end; margin-bottom: 10px;">
            <div style="background-color: rgba(255, 255, 255, 0.8); border-radius: 20px; padding: 10px;">👤</div>
            <div style="background-color: rgba(255, 255, 255, 0.8); border-radius: 20px; padding: 10px; margin-left: 10px;">👤</div>
            <div style="background-color: rgba(255, 255, 255, 0.8); border-radius: 20px; padding: 10px; margin-left: 10px; width: 200px;">👤</div>
        </div>
        <div>
            For more feedback
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                Click here!
            </button>
        </div>
    </div>
</footer>

<?php
if (isset($modals)) {
    echo Modals::layout('create', 'feedback');
} else {
    // Fallback if $modals is not available
    echo '<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Add New Room</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Form to add a new room will be here.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save Room</button>
                    </div>
                </div>
            </div>
        </div>';
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Your existing or new modal script (if you are not fully relying on Bootstrap's data-bs-toggle)
    const openModalButton = document.querySelector('[data-bs-toggle="modal"][data-bs-target="#createModal"]');
    const feedbackModal = document.getElementById('createModal');

    if (openModalButton && feedbackModal) {
        // Bootstrap's JavaScript will handle the click event automatically
        // You might add additional listeners here if needed for more complex modal behavior
    }
</script>
</html>