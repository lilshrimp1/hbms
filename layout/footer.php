
    <!-- DataTables JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.2.2/af-2.7.0/b-3.2.2/b-colvis-3.2.2/b-html5-3.2.2/b-print-3.2.2/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.4/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js" integrity="sha384-10kTwhFyUU637a6/7q0kLBdo8jQWjxteg63DT/K8Sdq/nCDaDAkH+Nq/MIrsp8wc" crossorigin="anonymous"></script>
    
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
                dom: '<"row"<"col-sm-2"l><"col-sm-6"B><"col-sm-4"f>>' +
             'rt' +
             '<"row"<"col-sm-12 mt-3"i><"col-sm-12 d-flex justify-content-start"p>>',
             buttons: [{
            extend: 'collection',
            text: 'Export',
            className: 'btn btn-outline-success px-3',  // Main Export button style
            buttons: [
                {
                    extend: 'copy',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'excel',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'csv',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'pdf',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'print',
                    className: 'dropdown-item-custom',
                }
            ]
        }],
        pagingType: 'simple_numbers'
        });
    });
    </script>

<script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                dom: '<"row"<"col-sm-2"l><"col-sm-6"B><"col-sm-4"f>>' +
             'rt' +
             '<"row"<"col-sm-12 mt-3"i><"col-sm-12 d-flex justify-content-start"p>>',
             buttons: [{
            extend: 'collection',
            text: 'Export',
            className: 'btn btn-outline-success px-3',  // Main Export button style
            buttons: [
                {
                    extend: 'copy',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'excel',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'csv',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'pdf',
                    className: 'dropdown-item-custom',
                },
                {
                    extend: 'print',
                    className: 'dropdown-item-custom',
                }
            ]
        }],
        pagingType: 'simple_numbers'
        });
    });
    </script>

    <script src="https://kit.fontawesome.com/de63ed52cd.js" crossorigin="anonymous"></script>
</body>
</html>