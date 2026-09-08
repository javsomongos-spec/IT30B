
CREATE TABLE IF NOT EXISTS borrow (
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,

    student_id INT NOT NULL,
    book_id INT NOT NULL,

    borrow_date TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    borrow_return_date TIMESTAMP NULL
        DEFAULT NULL,

    CONSTRAINT fk_borrow_student
        FOREIGN KEY (student_id)
        REFERENCES students(student_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_borrow_book
        FOREIGN KEY (book_id)
        REFERENCES books(book_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;

-- This is where the relationships are established.



INSERT INTO books (
    book_title,
    book_author,
    book_category
) VALUES 
   ('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction'),
    ('1984', 'George Orwell', 'Dystopian'),
    ('To Kill a Mockingbird', 'Harper Lee', 'Fiction');


    
INSERT INTO borrow (student_id, book_id)
VALUES
    (1, 1),
    (2, 2),
    (3, 3);