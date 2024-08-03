<?php
include '../config.php';
session_start();

if ($_SESSION['role'] != 'founder') {
    header("Location: ../index.php");
    exit();
}

$class_id = $_GET['class_id'];
$founder_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $due_title = $_POST['due_title'];
    $due_description = $_POST['due_description'];
    $due_date = $_POST['due_date'];
    $attachment = $_FILES['attachment']['name'];
    $target_dir = "../uploads/attachments/";
    $target_file = $target_dir . basename($attachment);
    move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file);

    $sql = "INSERT INTO dues (class_id, due_title, due_description, due_date, attachment, created_by) VALUES ('$class_id', '$due_title', '$due_description', '$due_date', '$target_file', '$founder_id')";
    if ($conn->query($sql) === TRUE) {
        echo "Due created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$dues = $conn->query("SELECT * FROM dues WHERE class_id='$class_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dues</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-gray-900 text-white">
    <?php include '../includes/navbar.php'; ?>
    <div class="container mx-auto mt-5">
        <h2 class="text-2xl">Manage Dues</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-4">
                <label for="due_title" class="block text-sm font-bold mb-2">Due Title</label>
                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="due_title" name="due_title" required>
            </div>
            <div class="mb-4">
                <label for="due_description" class="block text-sm font-bold mb-2">Due Description</label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="due_description" name="due_description" rows="3" required></textarea>
            </div>
            <div class="mb-4">
                <label for="due_date" class="block text-sm font-bold mb-2">Due Date</label>
                <input type="datetime-local" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="due_date" name="due_date" required>
            </div>
            <div class="mb-4">
                <label for="attachment" class="block text-sm font-bold mb-2">Upload Attachment</label>
                <input type="file" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="attachment" name="attachment">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create Due</button>
        </form>
        <h3 class="text-xl mt-5">Existing Dues</h3>
        <ul class="list-group">
            <?php while ($due = $dues->fetch_assoc()): ?>
                <li class="list-group-item">
                    <h5><?= htmlspecialchars($due['due_title']) ?></h5>
                    <p><?= htmlspecialchars($due['due_description']) ?></p>
                    <p><strong>Due Date:</strong> <?= htmlspecialchars($due['due_date']) ?></p>
                    <?php if (!empty($due['attachment'])): ?>
                        <a href="<?= $due['attachment'] ?>" class="text-blue-500 underline" target="_blank">View Attachment</a>
                    <?php endif; ?>
                    <a href="view_submissions.php?due_id=<?= $due['id'] ?>" class="text-blue-500 underline">View Submissions</a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mt-4">Back</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
