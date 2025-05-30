<?php

    class database{

        function opencon() {
            return new PDO(
                dsn: 'mysql:host=localhost;dbname=lms_app',
                username: 'root',
                password: ''
            );
        }

        function signupUser($firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password, $profile_picture_path) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                $stmt = $con->prepare("INSERT INTO Users (user_FN, user_LN, user_birthday, user_sex, user_email, user_phone, user_username, user_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstname, $lastname, $birthday, $sex, $email, $phone, $username, $password]);

                $user_id = $con->lastInsertId();

                $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
                $stmt->execute([$user_id, $profile_picture_path]);

                $con->commit();
                return $user_id;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function addAuthor($authorFirstName, $authorLastName, $authorBirthYear, $authorNationality) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                $stmt = $con->prepare("INSERT INTO Authors (author_FN, author_LN, author_birthday, author_nat) VALUES (?, ?, ?, ?)");
                $stmt->execute([$authorFirstName, $authorLastName, $authorBirthYear, $authorNationality]);

                $author_id = $con->lastInsertId();

                $con->commit();
                return $author_id;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function updateAuthor($authorID, $authorFirstName, $authorLastName, $authorBirthYear, $authorNationality) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                $stmt = $con->prepare("UPDATE Authors SET author_FN = ?, author_LN = ?, author_birthday = ?, author_nat = ? WHERE author_id = ?");
                $stmt->execute([$authorFirstName, $authorLastName, $authorBirthYear, $authorNationality, $authorID]);

                $con->commit();
                return true;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function addGenre($genreName) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                $stmt = $con->prepare("INSERT INTO Genres (genre_name) VALUES (?)");
                $stmt->execute([$genreName]);

                $genre_id = $con->lastInsertId();

                $con->commit();
                return $genre_id;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function updateGenre($genreID, $genreName) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                $stmt = $con->prepare("UPDATE Genres SET genre_name = ? WHERE genre_id = ?");
                $stmt->execute([$genreName, $genreID]);

                $con->commit();
                return true;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function addBook($bookTitle, $bookISBN, $bookYear, $bookQuantity, $genre_ids = [], $author_ids = []) {
            $con = $this->opencon();

            try {
                $con->beginTransaction();

                // Inserting into Books table
                $stmt = $con->prepare("INSERT INTO Books (book_title, book_isbn, book_pubyear, quantity_avail) VALUES (?, ?, ?, ?)");
                $stmt->execute([$bookTitle, $bookISBN, $bookYear, $bookQuantity]);
                $book_id = $con->lastInsertId();

                // Inserting into Genre_Books tables
                foreach ($genre_ids as $genre_id) {
                    $stmt = $con->prepare("INSERT INTO Genre_Books (genre_id, book_id) VALUES (?, ?)");
                    $stmt->execute([$genre_id, $book_id]);
                }

                // Inserting into Book_Authors tables
                foreach ($author_ids as $author_id) {
                    $stmt = $con->prepare("INSERT INTO Book_Authors (book_id, author_id) VALUES (?, ?)");
                    $stmt->execute([$book_id, $author_id]);
                }

                // Inserting into Book_Copies table
                for ($i = 0; $i < $bookQuantity; $i++) {
                    $stmt = $con->prepare("INSERT INTO Book_Copy (book_id, is_available) VALUES (?, 1)");
                    $stmt->execute([$book_id]);
                }

                $con->commit();
                return true;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function insertAddress($user_id, $street, $barangay, $city, $province) {
            $con = $this->opencon();

            try {
                $con-> beginTransaction();

                $stmt = $con->prepare("INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)");
                $stmt->execute([$street, $barangay, $city, $province]);

                $addressId = $con->lastInsertId();

                $stmt = $con->prepare("INSERT INTO Users_Address(user_id, address_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $addressId]);

                $con->commit();
                return true;
            } catch (PDOexception $e) {
                $con->rollBack();
                return false;
            }

        }

        function loginUser($email, $password) {
            $con = $this->opencon();
            $stmt = $con->prepare("SELECT * FROM Users WHERE user_email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && password_verify($password, $user['user_password'])) {
                    return $user;
                } else {
                    return false;
                }
        }

        function viewAuthors() {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Authors")->fetchAll();
        }

        function viewAuthorsID($id) {
            $con = $this->opencon();
            $stmt = $con->prepare("SELECT * FROM Authors WHERE author_id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function viewGenres() {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Genres ORDER BY genre_id")->fetchAll();
        }

        function viewGenresID($id) {
            $con = $this->opencon();
            $stmt = $con->prepare("SELECT * FROM Genres WHERE genre_id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function viewBooks() {
            $con = $this->opencon();
            return $con->query("SELECT * FROM Books")->fetchAll();
        }

        function viewBooksID($id) {
            $con = $this->opencon();
            $stmt = $con->prepare("SELECT * FROM Books WHERE book_id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


    }


?>