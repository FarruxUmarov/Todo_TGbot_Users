<?php

class User {

    public function create()
    {
        declare(strict_types=1);
        declare(strict_types=1);

        if (isset($_POST['email']) && isset($_POST['password'])){
        
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            
            $db = DB::connect();
            
            $stmt = $db->prepare("INSERT INTO users(email, password) VALUES (:email, :password)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $result = $stmt->execute();
            
            echo 'full';
        }
if (isset($_POST['email']) && isset($_POST['password'])){

    $email = $_POST['email'];
    $password = $_POST['password'];
    
    
    $db = DB::connect();
    
    $stmt = $db->prepare("INSERT INTO users(email, password) VALUES (:email, :password)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $result = $stmt->execute();
    
    echo 'full';
}
    }
}