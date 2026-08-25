CREATE DATABASE <database_name>;
SHOW DATABASES;
CONNECT TABLE <table_name_in_plural>;
INSERT INTO <table_name_in_plural>;
     (colums) VALUES
          (values);
  

#UTILITY COMMANDS 
c1s
mysqldump -u root -p --databases library >"D:\xampp\htdocs\dev\IT30B\backups\date:~-4%_%date:~4,2%_%time:~7,2%_%time:~0,2%_%time:~3,2%_%time:~6,2%_library.sql"


SELECT br.borrow_id, s.student_id,
     CONCAT(s.student_first_name, ' ', s.student_last_name) AS student_name, s.student_course,

     b.book_title, b.book_author, b.book_category,
     br.borrow_date
FROM borrow br 
     JOIN student s ON br.student_id  = s, student_id
     JOIN book b ON br.book_id = b,book_id
ORDER BY br.borrow_date DESC;



CREATE TABLE borrow (
     borrow_id INT PRIMARY KEY AUTO_INCREMENT,
     student_id INT NOT NULL,
     book_id INT NOT NULL,
     borrow_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     borrow_return_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     CONSTRAINT fk_student FOREIGN KEY (student_id) REFERENCES student(student_id),
     CONSTRAINT fk_book FOREIGN KEY (book_id) REFERENCES books(book_id)


);

mysqldump -u root -p --databases library_db > "C:\IT30B\backups\%date:~-4%_%date:~4,2%_%date:~7,2%_%time:~0,2%_%time:~3,2%_%time:~6,2%_library_db.sql"