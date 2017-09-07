<?php
// Routes
include("localDB.php");

// $app->get('/[{name}]', function ($request, $response, $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");

//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

//login
$app->post('/login/', function ($request, $response, $args) {
  // $dbRemote = $this->db_remote;
  $db = $this->db_local;
  $loginParams = $request->getParsedBody();
  $loginData = array();

  $username = $loginParams['username'];
  $password = $loginParams['password'];
  // prepare sql and bind parameters
  
  $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':password', $password);
  
  //Check whether the query was successful or not
  if($stmt->execute()) {
    $usertype="";
    $result = $stmt->fetchAll();
    foreach ($result as $rowValue) {
      
    // $usertype= $rowValue['usertype'];
    
    // }
      if(!empty($usertype)) {
      //     $dbval = 
      $stmt->fetch(PDO::FETCH_ASSOC);
        
        $status = "success";
        $loginData[] = array("status" => $status);
        //record login history in history_tbl (not priority)
        // $stmt = $db->prepare("INSERT INTO attendance_tbl (name,user_id)VALUES(:name,:user_id)");
        // $stmt->bindParam(':name', $name);
        // $stmt->bindParam(':user_id', $user_id);
        // $stmt->execute();
        //       echo json_encode($loginData);
      }else{
        $status = "failed";
        $usertype = "none";
        $loginData[] = array("status" => $status);
        //       $loginData[] = array("status" => $status, "usertype" => $usertype);
        echo json_encode($loginData);
      }
  // }else{
  //   $status = "sql error";
  //   $usertype = "none";
  //   $loginData[] = array("status" => $status, "usertype" => $usertype);
    // echo json_encode($loginData);
    }
  }
});

//get Employees List
$app->get('/getEmployee/', function ($request, $response, $args) {
  $db = $this->db_local;
  $empListData = array();

  // $username = $userParams['username'];
  // prepare sql and bind parameters
  $stmt = $db->prepare("SELECT *FROM users");
  $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $row){
      $empListData[] = array(
        "user_id"          => $row['user_id'],
        "username"          => $row['username'],
        "usertype"          => $row['usertype']
      );
    }
    echo json_encode($empListData);
});

//Add Employee
$app->post('/addEmployee/', function ($request, $response) {
   
   try{
       $db = $this->db_local;
       $sql = "INSERT INTO users(username,usertype,password) VALUES (:username,:usertype,:password)";
       $pre  = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
       $values = array(
       ':username' => $request->getParam('username'),
       ':usertype' => $request->getParam('usertype'),
       //Using hash for password encryption
       'password' => password_hash($request->getParam('password'),PASSWORD_DEFAULT)
       );
       $result = $pre->execute($values);
       return $response->withJson(array('status' => 'User Created'),200);
       
   }
   catch(\Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
   
});

//Edit Employee Info
$app->post('/updateEmployee/', function ($request, $response, $args){
    $editParams = $request->getParsedBody();
    // $emp = json_decode($request->getBody());
    $id = $request->getAttribute('id');
    $name = $editParams['name'];
    $salary = $editParams['salary'];
    $age = $editParams['age'];
    $id = $editParams['id'];
    $sql = "UPDATE employee SET employee_name=:name, employee_salary=:salary, employee_age=:age WHERE id=:id";
    try {
        $db = $this->db_local;
        // $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':salary', $salary);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        // $db = null;
        echo json_encode($editParams);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});

//Delete Employee Information
$app->delete('/delEmployee/{id}', function ($request, $response, $args){
    $id = $request->getAttribute('id');
    // $id = $delParams['id'];
    $sql = "DELETE FROM employee WHERE id=:id";
    try {
        $db = $this->db_local;
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        // $db = null;
     echo '{"error":{"text":"successfully! deleted Records"}}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});

//Add Atendee
$app->post('/addAttendee/', function ($request, $response) {
   
   try{
       $db = $this->db_local;
       $sql = "INSERT INTO attendance_tbl(user_id,usertype,password) VALUES (:username,:usertype,:password)";
       $pre  = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
       $values = array(
       ':username' => $request->getParam('username'),
       ':usertype' => $request->getParam('usertype'),
       //Using hash for password encryption
       'password' => password_hash($request->getParam('password'),PASSWORD_DEFAULT)
       );
       $result = $pre->execute($values);
       return $response->withJson(array('status' => 'User Created'),200);
       
   }
   catch(\Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
   
});
