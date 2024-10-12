<?php
// Connect to the Database
$servername = "localhost";
$username = "root";
$password = "";
$database = "notes";

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Die if connection was not successful   
if (!$conn) {
    die("Sorry we failed to connect: " . mysqli_connect_error());
}

// Handle Deleting Notes
if (isset($_GET['delete'])) {
    $slno = $_GET['delete'];
    $sql = "DELETE FROM `notes` WHERE `slno` = $slno";
    $result = mysqli_query($conn, $sql);
}

// Handle Inserting and Updating Notes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['snoEdit'])) {
        // Update the record
        $slno = $_POST['snoEdit'];
        $title = $_POST['titleEdit'];
        $desc = $_POST['descEdit'];
        $photo = '';

        // Handle the photo upload if provided
        if (isset($_FILES['photoEdit']) && $_FILES['photoEdit']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['photoEdit']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_extensions)) {
                if (move_uploaded_file($_FILES['photoEdit']['tmp_name'], $target_file)) {
                    $photo = $target_file;
                }
            }
        }

        // Update query
        $sql = "UPDATE `notes` SET `title`='$title', `description`='$desc'";
        if ($photo != '') {
            $sql .= ", `photo`='$photo'";
        }
        $sql .= " WHERE `slno`=$slno";
        $result = mysqli_query($conn, $sql);
    } else {
        // Insert the new note
        $title = $_POST['title'];
        $desc = $_POST['desc'];
        $photo = '';

        // Handle the photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['photo']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_extensions)) {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                    $photo = $target_file;
                }
            }
        }

        $sql = "INSERT INTO `notes` (`title`, `description`, `photo`) VALUES ('$title', '$desc', '$photo')";
        $result = mysqli_query($conn, $sql);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notes with Photo Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container my-3">
        <h2>Add a Note</h2>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="title" name="title">
            </div>
            <div class="mb-3">
                <label for="desc" class="form-label">Note Description</label>
                <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Upload a Photo</label>
                <input type="file" class="form-control" id="photo" name="photo">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="snoEdit" id="snoEdit">
                        <div class="mb-3">
                            <label for="titleEdit" class="form-label">Note Title</label>
                            <input type="text" class="form-control" id="titleEdit" name="titleEdit">
                        </div>
                        <div class="mb-3">
                            <label for="descEdit" class="form-label">Note Description</label>
                            <textarea class="form-control" id="descEdit" name="descEdit" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="photoEdit" class="form-label">Upload a New Photo</label>
                            <input type="file" class="form-control" id="photoEdit" name="photoEdit">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Note</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Your Notes</h2>
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM `notes`";
                $result = mysqli_query($conn, $sql);
                $serial_number = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                    <th scope='row'>" . $serial_number . "</th>
                    <td>" . $row['title'] . "</td>
                    <td>" . $row['description'] . "</td>
                    <td><img src='" . $row['photo'] . "'></td>
                    <td>
                        <button class='edit btn btn-sm btn-primary' data-id='" . $row['slno'] . "'>Edit</button>
                        <button class='delete btn btn-sm btn-danger' data-id='" . $row['slno'] . "'>Delete</button>
                    </td>
                </tr>";
                    $serial_number++;
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#myTable').DataTable();

            // Edit button click event
            $('.edit').on('click', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: 'fetch_single_note.php',
                    type: 'POST',
                    data: { slno: id },
                    success: function (response) {
                        var note = JSON.parse(response);
                        $('#snoEdit').val(note.slno);
                        $('#titleEdit').val(note.title);
                        $('#descEdit').val(note.description);
                        $('#editModal').modal('show');
                    }
                });
            });

            // Delete button click event
            $('.delete').on('click', function () {
                var id = $(this).data('id');
                if (confirm("Are you sure you want to delete this note?")) {
                    window.location = `index.php?delete=${id}`;
                }
            });
        });
    </script>
</body>
</html>
