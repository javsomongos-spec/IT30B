<?php
//Database connection
$host = "localhost";
$db = 'IT30B_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
      echo "Database connection successful!";
} catch (PDOException $e) {
    die ("database connection failed: " . $e->getMessage());

}

//session
session_start();

//determine the current page
$section = $_GET['section'] ?? 'student'; 

//determine the crud operation
$action = $_GET['action'] ?? 'list';

// fetch student
if ($section == 'student') {



       $stmt = $pdo->("SELECT * FROM student
       order by student_id DESC;
       ");

  students = $stmt->fetchAll();
}

//create student
if ($section == 'student' && $action == 'create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstname = trim($_POST['first_name']?? '');
        $lastname = trim($_POST['last_name']?? ''  );
        $course = trim($_POST['course']?? ''  );
      
        if($firstname !== '' && $lastname !== '' && $course !== '') {
            $sql = "
            INSERT INTO student(
                first_name,
                last_name,
                course
            ) VALUES(?,?,?)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
         $firstname,
         $lastname,
          $course
          ]);

          header('Location: index.php?section=student');
          exit;
}
    }
}







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

 <h1>simple library system</h1>
 <nav>
     <ahref="index.php?section=student">student</a>
     <a href="index.php?section=book">Books</a>
     <a href="index.php?section=borrow">Borrow</a>  

     </nav>
     <hr>
     <?php if ($section == 'student'): ?>
        <h1>Student</h1>

        <p>
            <a href="index.php?section=student&action=create">
                Add new student
            </a>
        </p>
        <?php if($action == 'create'): ?>
            <h2>create student</h2>
           
        <p>
            <a href="index.php?section=student&action=create">
                Add new student
            </a>
        </p>
        <?php if($action == 'create'): ?>
            <h2>create student</h2>
           
            <form method="POST">
                <label> First Name</label>
                <br>
                <input type="text" name="first_name" required>
            />
        </p>

        <p>
            <label> Last Name</label>
                <br>
                <input type="text" name="last_name" required>

                />
        </p>
        <p>
            <label> Course</label>
                <br>
                <input type="text" name="course" required>

                />
        </p>
        <p>
            <button type="submit">
                Save
            </button>  

            <a href="index.php?section=student">
                Cancel
            </a>
        </form>
  <?php else: ?>
 <?php endif; ?>



            <?php else: ?>
                <h2>List of students</h2>
             <table>
                <thred>
                    
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Course</th>
                        <th>created at</th>
                        <th>Action</th>
                    </tr>
                    </thred>
                    <tbody>
                         <?php foreach ($students as $student): ?>
                            <tr>  
                                <td>
                                      <?=htmlspecialchars($student['student_id'])?>
                                </td>
                                <td>
                                      <?=htmlspecialchars($student['first_name'])?> 
                                </td>
                                <td>
                                      <?=htmlspecialchars($student['last_name'])?>  
                                </td>
                                <td>    
                                      <?=htmlspecialchars($student['course'])?>
                                </td>
                                <td>
                                      <?=htmlspecialchars($student['created_at'])?>
                                </td>
                                <td> 
                                    <a href="index.php?section=student&action=edit&id=<?= $student['student_id'] ?>">Edit</a>
                                    |
                                    <a href="index.php?section=student&action=delete&id=<?= $student['student_id'] ?>">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
               <?php endif; ?>

     <?php endif; ?>
     <?php if ($section == 'books'): ?>
        <h1>Books</h1>
     <?php endif; ?>
     <?php if ($section == 'borrow'): ?>
        <h1>Borrow</h1>
     <?php endif; ?>
     <?
</body>
</html>