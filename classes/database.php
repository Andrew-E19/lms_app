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

                $userId = $con->lastInsertId();

                $stmt = $con->prepare("INSERT INTO users_pictures (user_id, user_pic_url) VALUES (?, ?)");
                $stmt->execute([$userId, $profile_picture_path]);

                $con->commit();
                return $userId;
            } catch (PDOException $e) {
                $con->rollBack();
                return false;
            }
        }

        function insertAddress($userId, $street, $barangay, $city, $province) {
            $con = $this->opencon();

            try {
                $con-> beginTransaction();

                $stmt = $con->prepare("INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) VALUES (?, ?, ?, ?)");
                $stmt->execute([$street, $barangay, $city, $province]);

                $addressId = $con->lastInsertId();

                $stmt = $con->prepare("INSERT INTO Users_Address(user_id, address_id) VALUES (?, ?)");
                $stmt->execute([$userId, $addressId]);

                $con->commit();
                return true;
            } catch (PDOexception $e) {
                $con->rollBack();
                return false;
            }

        }

    }


?>