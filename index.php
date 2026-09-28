<?php
//Database connection
$host = "localhost";
$db = 'it30b_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// Session
session_start();


// Determine current section
$section = $_GET['section'] ?? 'student';


// Determine CRUD operation
$action = $_GET['action'] ?? '';


// Fetch Students
$students = [];

if ($section === 'student') {

    $stmt = $pdo->query("
        SELECT *
        FROM student
        ORDER BY student_id DESC
    ");

    $students = $stmt->fetchAll();
}

// Create Student
if ($section === 'student' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = $_POST['student_first_name'] ?? '';
        $lastName = $_POST['student_last_name'] ?? '';
        $course = $_POST['student_course'] ?? '';

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $sql = "
                INSERT INTO student (
                    student_first_name,
                    student_last_name,
                    student_course
                )
                VALUES (?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $firstName,
                $lastName,
                $course
            ]);

            header("Location: index.php?section=student");
            exit;
        }
    }
}

// Update Student
if($section === 'student' && $action === 'update'){

    $studentId = (int) ($_GET['id']) ?? 00;
    
    // Update student on post
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $firstName = $_POST['student_first_name'] ?? '';
        $lastName = $_POST['student_last_name'] ?? '';
        $course = $_POST['student_course'] ?? '';

        $sql=("
            UPDATE STUDENT
            SET 
                student_first_name = ?,
                student_last_name = ?,
                student_course = ?
            WHERE student_id = ?
        ");

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
                $firstName,
                $lastName,
                $course,
                $studentId
        ]);

        header("Location: index.php?section=student");
        exit;
    }

    // Retrieve student info

    $stmt = $pdo->prepare("
         SELECT *
         FROM student
         WHERE student_id = ?
         
    ");

    $stmt->execute([$studentId]);

    $student = $stmt->fetch();


    if(!$student){
        die("Student Not Found");

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Library System</title>
</head>

<body>

    <h1>Simple Library System</h1>

    <nav>
        <a href="index.php?section=students">
            Student
        </a>

        <a href="index.php?section=books">
            Books
        </a>

        <a href="index.php?section=borrow">
            Borrow
        </a>
    </nav>

    <hr>


    <?php if ($section === 'student'): ?>

        <h2>Student</h2>

        <p>
            <a href="index.php?section=student&action=create">
                Add New Student
            </a>
        </p>


        <?php if ($action === 'create'): ?>

            <h3>Create Student</h3>

            <form method="POST">

                <p>
                    <label for="student_first_name">
                        First Name
                    </label>

                    <br>

                    <input
                        type="text"
                        id="student_first_name"
                        name="student_first_name"
                        required
                    >
                </p>


                <p>
                    <label for="student_last_name">
                        Last Name
                    </label>

                    <br>

                    <input
                        type="text"
                        id="student_last_name"
                        name="student_last_name"
                        required
                    >
                </p>


                <p>
                    <label for="student_course">
                        Course
                    </label>

                    <br>

                    <input
                        type="text"
                        id="student_course"
                        name="student_course"
                        required
                    >
                </p>


                <button type="submit">
                    Save
                </button>

                <a href="index.php?section=students">
                    Cancel
                </a>

            </form>
        <?php elseif($action==="update"): ?>
            <h2>Update Student Info<h2>
            <form method="POST">

             
                <p>
                    <label for="student_first_name">
                        First Name
                    </label>

                    <br>

                    <input
                        type="text"
                        id="student_first_name"
                        name="student_first_name"
                        value="<?= htmlspecialchars($student['student_first_name']) ?>"
                        required
                    >
                </p>


                <p>
                    <label for="student_last_name">
                        Last Name
                    </label>

                    <br>

                    <input
                        type="text"
                        id="student_last_name"
                        name="student_last_name"
                        value="<?= htmlspecialchars($student['student_last_name']) ?>"
                        required
                    >
                </p>


                <p>
                    <label for="student_course">
                        Course
                    </label>
                    <br>

                    <input
                        type="text"
                        id="student_course"
                        name="student_course"
                        value="<?= htmlspecialchars($student['student_course']) ?>"
                        required
                    >
                </p>


                <button type="submit">
                    Save
                </button>

                <a href="index.php?section=student">
                    Cancel
                </a>
        </form>
        <?php else: ?>

            <table border="1">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Course</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($students as $student): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($student['student_id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student['student_first_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student['student_last_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student['student_course']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($student['student_create_at']) ?>
                            </td>

                            <td>

                                <a href="index.php?section=student&action=update&id=<?= $student['student_id'] ?>">
                                    Edit
                                </a>

                                <a href="#">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>


        <?php endif; ?>


    <?php elseif ($section === 'books'): ?>

        <h2>Books</h2>

      


    <?php elseif ($section === 'borrow'): ?>

        <h2>Borrow</h2>



    <?php endif; ?>


</body>

</html>