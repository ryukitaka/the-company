<?php
require_once "Database.php";

class User extends Database {
    // create
    // $request holds the data from the FORM
    public function store($request) {
        // save the user to the db
        /*
            $_POST['first_name'];   ->   $request['first_name'];
            $_POST['last_name'];    ->   $request['last_name'];
            $_POST['username'];     ->   $request['username'];
            $_POST['password'];     ->   $request['password'];
        */ 
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $password   = $request['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, password) 
                VALUES ('$first_name', '$last_name', '$username', '$password')";

        if($this->conn->query($sql)){
            header('location: ../views');   // go to index.php which is the login page
            exit;
        } else {
            die('Error creating the user: ' . $this->conn->error);
        }

    }

    // read
    // login()
    public function login($request)
    {
        /*
            $request will catch the data from the $_POST in actions>login.php

            $_POST['username'];    -->    $request['username'];
            $_POST['password'];    -->    $request['password'];
        */ 

        $username = $request['username'];
        $password = $request['password'];

        $sql = "SELECT * FROM users WHERE username = '$username'";

        $result = $this->conn->query($sql);
        // $result holds the record of the user

        # check the username
        if ($result->num_rows == 1){
            
            $user = $result->fetch_assoc();
            // $user = ['id' => 1, 'first_name' => 'Ayami', 'last_name' => 'Minemura', 'username' => 'ayami', 'password' => '$2y$10$1', 'photo' => NULL]
            /*
                $user is now the array name

                $user['id'];   get the value 1
                $user['username'];    get the value 'ayami'
                $user['first_name']   get the value 'Ayami'
                $user['last_name']    get the value 'Minemura'
                $user['password']     get the value of password from the database
            */ 

            #check if the password is correct
            if (password_verify($password, $user['password'])){
                # Create session variables for future use.
                session_start();
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['first_name'] . " " . $user['last_name']; // 'Ayami Minemura'

                header('location: ../views/dashboard.php');
                exit;
            } else {
                die('Password is incorrect');
            }
        } else {
            die('Username not found.');
        }

    }


    // logout()
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');
        exit;
    }


    // getAllUsers()
    public function getAllUsers()
    {
        $sql = "SELECT id, first_name, last_name, username, photo FROM users";

        if ($result = $this->conn->query($sql)){
            // $result holds all the users from the database
            return $result;
        } else {
            die('Error retrieving all users' . $this->conn->error);
        }
    }


    // getUser()
    public function getUser()
    {

        $id = $_SESSION['id'];

        $sql = "SELECT first_name, last_name, username, photo FROM users WHERE id = $id";

        if ($result = $this->conn->query($sql)){
            return $result->fetch_assoc();
            // ['first_name' => 'Ayami', 'last_name' => 'Minemura', 'username' => 'ayami', 'photo' => 'NULL'];
        } else {
            die('Error retrieving the user: ' . $this->conn->error);
        }
    }


    // update
    // update()
    public function update($request, $files)
    {
        /*
            $_POST['first_name'];    -->  $request['first_name'];
            $_POST['last_name'];    -->   $request['last_name'];
            $_POST['username'];    -->    $request['username'];
        */

        /*
            $_FILES - is a 2D associative array

            $_FILES['photo']['name'];      -->    $files['photo']['name']; // name of the image
            $_FILES['photo']['tmp_name'];       -->    $files['photo']['tmp_name'];  // actual image from the temporary storage
        */ 

        session_start();
        $id         = $_SESSION['id'];
        $first_name = $request['first_name'];
        $last_name  = $request['last_name'];
        $username   = $request['username'];
        $photo_name = $files['photo']['name']; // holds the name of the image
        $tmp_photo  = $files['photo']['tmp_name']; // holds the image from temporary storage
        // ['photo']    - is the name of our input type file
        // ['name']     - is the actual name of the image
        // ['tmp_name'] - is the temporary storage of the image before it will be saved in assets/images folder permanently.

        $sql = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id = $id";

        if ($this->conn->query($sql)){
            $_SESSION['username']     = $username;
            $_SESSION['full_name']    = "$first_name $last_name";

            # If there is an uploaded photo, save the name to db and save the actual image to assets/images folder.
            if ($photo_name){
                $sql = "UPDATE users SET photo = '$photo_name' WHERE id = $id";
                
                $destination = "../assets/images/$photo_name";

                # Save the image name to db
                if ($this->conn->query($sql)){
                    # Save the actual image to assets/images folder
                    if (move_uploaded_file($tmp_photo, $destination)){
                        header('location: ../views/dashboard.php');
                        exit;
                    } else {
                        die('Error moving the photo.');
                    }
                } else {
                    die('Error uploading photo: ' . $this->conn->error);
                }
            }
            header('location: ../views/dashboard.php');
            exit;
        } else {
            die('Error updating the user: ' . $this->conn->error);
        }
    }



    // delete
    // delete()
    public function delete()
    {
        session_start();
        $id = $_SESSION['id'];

        $sql = "DELETE FROM users WHERE id = $id";

        if ($this->conn->query($sql)){
            $this->logout();
        } else {
            die('Error deleting your account: ' . $this->conn->error);
        }
    }
}