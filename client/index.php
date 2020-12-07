<?php

// Remote API url
$api_URL = 'https://reqres.in/api/users?page=1';

// Retrieve users list from the API
$res = file_get_contents($api_URL);

// Decode the returned JSON sting
$response_array = json_decode($res, 1);

$users = $response_array['data'];

?><!DOCTYPE html>
<html lang="en">
<head>
  <title>Listing users using Bootstrap table</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">            
  <table class="table table-hover">
    <thead>
      <tr>
        <th>ID</th>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Email</th>
        <th>Avatar</th>
      </tr>
    </thead>
    <tbody>
        <h2 style="text-align: center; margin:40px;">Users list from reqres API</h2>
        <?php
        foreach($users as $user) {
            ?>
            <tr>
                <td><?=$user['id']?></td>
                <td><?=$user['first_name']?></td>
                <td><?=$user['last_name']?></td>
                <td><?=$user['email']?></td>
                <td><img src="<?=$user['avatar']?>" height="50" /></td>
            </tr>
            <?php
        }
        ?>
        
    </tbody>
  </table>
</div>

</body>
</html>
