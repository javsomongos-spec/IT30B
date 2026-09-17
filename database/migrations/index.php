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
                                    <a>Edit</a>
                                    I
                                    <a>Delete</a>
                            </td>
                            </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>


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