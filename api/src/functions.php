<?php

include("localDB.php");

function login()                                                    
{
    $request = Slim::getInstance()->request();
    $user = json_decode($request->getBody());
    $email= $user->email;
    $password= $user->password;

if(!empty($email)&&!empty($password))
    {	
       $db = $this->db_local;
        $sql="SELECT username, password FROM users WHERE username='$username' and password='$password'";
        $db = getConnection();
    try {
        $result=$db->query($sql); 

                if (!$result) { // add this check.
                      die('Invalid query: ' . mysql_error());
                }
        $row["users"]= $result->fetchAll(PDO::FETCH_OBJ);
        $db=null;
        echo json_encode($row);

    } catch(PDOException $e) 
    {
        error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    }
}

?>