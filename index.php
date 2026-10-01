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


$books = [];
$book = null;

if ($section === 'books') {

    $stmt = $pdo->query("
        SELECT *
        FROM books
        ORDER BY book_id DESC
    ");

    $books = $stmt->fetchAll();


    if ($action === 'update' && isset($_GET['id'])) {

        $id = $_GET['id'];

        $stmt = $pdo->prepare("
            SELECT *
            FROM books
            WHERE book_id = ?
        ");

        $stmt->execute([$id]);

        $book = $stmt->fetch();
    }
}


if ($section === 'books' && $action === 'update') {

    $bookId = (int) ($_GET['id'] ?? 0);

    // Update book on POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookTitle = $_POST['book_title'] ?? '';
        $bookAuthor = $_POST['book_author'] ?? '';
        $bookCategory = $_POST['book_category'] ?? '';

        $sql = ("
            UPDATE BOOKS
            SET
                book_title = ?,
                book_author = ?,
                book_category = ?
            WHERE book_id = ?
        ");

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $bookTitle,
            $bookAuthor,
            $bookCategory,
            $bookId
        ]);

        header("Location: index.php?section=books");
        exit;
    }
}

// RETRIEVE BORROWED BOOKS
if($section=="borrow"){

    // Retrieve students
    $stmt = $pdo->prepare("
        SELECT
            student_id,
            student_first_name,
            student_last_name
        FROM student
        ORDER BY student_last_name, student_first_name
    ");

    $students = $stmt->fetchAll();

    // Retrieve books
    $stmt = $pdo->prepare("
        SELECT
            book_id,
            book_title,
            book_author
        FROM books
        ORDER BY book_title
    ");

    $books = $stmt->fetchAll();

}

// create borrow

if($section==='borrow' && $action==='create'){
     
      if ($_SERVER['REQUEST_METHOD'] === 'POST'){

       $studentId = (int)  ($_POST['student_id'] ?? 00);
       $bookId =    (int)   ($S_POST['book_id'] ?? 00);

       if ($studentId >0 && $bookId >0){

             //vheck if student has  a unreturened book
             $stmt = $pdo-> prepare("
             SELECT borrrow_id
             FROM borrow
             WHERE student_id=?
                 AND BORROW_return_date IS NULL
                 LIMIT 1
            ");

            $stmt->execute([$studentId]);
            $studentBorrow = $stmt->fetch();


            if($studentBorrow){
                   $_SESSION['alert']= "this student cannot borrow another book because a previous book has not been return.";
            }else{
                //check if book is alredady returned
                $stmt = $pdo->prepare("
                SELECT borrow_id
                FROM borrow
                WHERE book_id =?
                  AND BORROW_return_date IS NULL
                  LIMIT 1
                  ");


              $stmt->execute([borrowId]);
              
              $bookBorrow = $stmt->fetch();

              if($bookBorrow){
                $_SESSION['alert'] = "this book cannot be borrowed because it has not been returned";
              }else{
                //create borroewd record 
                $stmt= $pdo-prepare("
                INSERT INTO borrow{
                  student_id,
                  book_id
                  )

                  VALUES(?,?)
                  ");
                  $stmt->execute([
                    $studentId,
                    $bookId
                  ]);


                  $_SESSION["alert"]= "book borrewed succesfully";


              }
            }

            header("location: index.php?section=borrow");
            exit;
       }
      }
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
        <a href="index.php?section=student">
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

                <a href="index.php?section=student">
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

<?php if ($action === 'create'): ?>

    <h3>Create Books</h3>

    <form method="POST">

        <p>
            <label for="book_title">
                Book Title
            </label>

            <br>

            <input
                type="text"
                id="book_title"
                name="book_title"
                required
            >
        </p>


        <p>
            <label for="book_author">
                Author
            </label>

            <br>

            <input
                type="text"
                id="book_author"
                name="book_author"
                required
            >
        </p>


        <p>
            <label for="book_category">
                Category
            </label>

            <br>

            <input
                type="text"
                id="book_category"
                name="book_category"
                required
            >
        </p>


        <button type="submit">
            Save
        </button>

        <a href="index.php?section=books">
            Cancel
        </a>

    </form>


<?php elseif ($action === "update"): ?>

    <h2>Update Books Info</h2>

    <form method="POST">

        <p>
            <label for="book_title">
                Book Title
            </label>

            <br>

            <input
                type="text"
                id="book_title"
                name="book_title"
                value="<?= htmlspecialchars($book['book_title']) ?>"
                required
            >
        </p>


        <p>
            <label for="book_author">
                Author
            </label>

            <br>

            <input
                type="text"
                id="book_author"
                name="book_author"
                value="<?= htmlspecialchars($book['book_author']) ?>"
                required
            >
        </p>


        <p>
            <label for="book_category">
                Category
            </label>

            <br>

            <input
                type="text"
                id="book_category"
                name="book_category"
                value="<?= htmlspecialchars($book['book_category']) ?>"
                required
            >
        </p>


        <button type="submit">
            Save
        </button>

        <a href="index.php?section=books">
            Cancel
        </a>

    </form>


<?php else: ?>

    <table border="1">

        <thead>

            <tr>
                <th>Book ID</th>
                <th>Book Title</th>
                <th>Book Author</th>
                <th>Book Category</th>
                <th>Book Created At</th>
                <th>Actions</th>
            </tr>

        </thead>


        <tbody>

            <?php foreach ($books as $book): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($book['book_id']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['book_title']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['book_author']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['book_category']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['book_created_at']) ?>
                    </td>

                    <td>

                     <a href="index.php?section=books&action=update&id=<?= $book['book_id'] ?>">
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

      


    <?php elseif ($section === 'borrow'): ?>

        <h2>Borrow</h2>


           <p>
               <a href="index.php?section=borrow&action=create">
                borrow a book

               </a>
           </p>
            <?php if ([$action=="create"]):?>
                <h2>borrow a book<h2>


                    <form method= "POST">
            </form>
            


            <?php endif; ?>

    <?php endif; ?>


</body>
<?php if (isset($_SESSION['alert'])): ?>

    <script>
        alert(<?= json_encode($_SESSION['alert']) ?>);
    </script>

    <?php unset($_SESSION['alert']); ?>

<?php endif; ?>
</html>