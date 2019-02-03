<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 01.02.2019
 * Time: 21:07
 */

function connect()
{
    $db = new mysqli("localhost", "root", "", "instantgramdb");
    if ($db->errno > 0)
        die("something broke");
    $db->set_charset("utf8");
    return $db;
}

function register()
{
    $db = connect();

    if (isset($_POST["btnSubmitRegistered"])) {
        if (empty($_POST["uname"]) || empty($_POST["psw"]) || empty($_POST["repsw"])) {


            echo "<p class='warning'>Fill the form</p>";

        } else if ($_POST["psw"] != $_POST["repsw"]) {
            echo "<p class='warning'>Passwords do not match.</p>";
        } else {
            $uname = $_POST["uname"];
            $psw = password_hash($_POST["psw"], PASSWORD_BCRYPT);
            $date = date('Y-m-d H:i:s');
            $isAdmin = 0;
            $error = 0;

            $sql = "SELECT * FROM user WHERE username='" . $uname . "'";

            if ($data = $db->query($sql)) {
                if ($data->num_rows > 0) {
                    $error = 1;
                }

            }

            if ($error == 1) {
                echo "<p class='warning'>This username is already taken.</p>";
            } else {

                $sql5 = "INSERT INTO user (username, password, created, isAdmin) VALUES (?,?,?,?);";

                if ($stmt = $db->prepare($sql5)) {

                    $stmt->bind_param("sssi", $uname, $psw, $date, $isAdmin);
                    $stmt->execute();
                    $id = $stmt->insert_id;

                    echo "<p class='warning'>Registration complete.</p>";

                }
            }
        }
    }
}

function login()
{
    $db = connect();
    if (!empty($_POST["uname"]) && !empty($_POST["psw"])) {
        $uname = $_POST["uname"];
        $psw = $_POST["psw"];

        $stmt = $db->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();
        $line = $result->fetch_assoc();


        if (!empty($line) && password_verify($psw, $line["password"])) {

            $_SESSION["login"] = $uname;
            $_SESSION["id"] = $line["ID_user"];
            $_SESSION["isAdmin"] = $line["isAdmin"];

            header("Location: index.php?page=publicGallery");
        } else {
            echo "<p class='warning'>Wrong login, try again.</p>";
        }

    }

}

function changeCredentials()
{
    $db = connect();
    if (!empty($_POST["uname"]) && $_POST["uname"] != $_SESSION["login"]) {
        $uname = $_POST["uname"];
        $id = $_SESSION["id"];

        $sql = "SELECT * FROM user WHERE username='" . $uname . "'";

        if ($data = $db->query($sql)) {
            if ($data->num_rows > 0) {
                echo "<p class='warning'>Username already in use.</p>";
            } else {

                $stmt = $db->prepare("UPDATE user SET username=? WHERE ID_user=?");
                $stmt->bind_param("si", $uname, $id);
                $stmt->execute();
                $_SESSION["login"] = $uname;
                header("Refresh:0");

            }

        }


    }

    if (!empty($_POST["psw"])) {
        $psw = password_hash($_POST["psw"], PASSWORD_BCRYPT);
        $id = $_SESSION["id"];
        $stmt = $db->prepare("UPDATE user SET password=? WHERE ID_user=?");
        $stmt->bind_param("si", $psw, $id);
        $stmt->execute();

    }


}


function isLogged()
{
    @session_start();

    if (isset($_SESSION["login"])) {
        return 1;
    }
    return 0;

}

function logout()
{
    @session_destroy();
    header("Location: index.php");
}


function chooseHeader()
{
    if (isLogged()) {

        include "header_2.php";
    } else {
        include "header_1.php";
    }
}

function indexRedirect()
{

    chooseHeader();

    if (isset($_GET["page"])) {
        if ($_GET["page"] == "login" || $_GET["page"] == "register") {
            include "hero.php";
        }

        $file = "./" . $_GET["page"] . ".php";

    } else {
        include "hero.php";
    }
    if (isset($file) && file_exists($file)) {
        include $file;
    }
}

function findName($id)
{
    $db = connect();

    $sql = "SELECT * FROM user WHERE ID_user=$id";
    if ($data = $db->query($sql)) {
        if ($data->num_rows == 0) {
            echo "<p class='warningAbsolute'>User not found.</p>";
        } else {


            while ($row = $data->fetch_assoc()) {
                return $row["username"];

            }
        }


    }
}


function findUser()
{
    if (isset($_GET["search"])) {
        $uname = $_GET["search"];
        $db = connect();

        $sql = "SELECT * FROM user WHERE username='" . $uname . "'";

        if ($data = $db->query($sql)) {
            if ($data->num_rows == 0) {
                echo "<p class='warningAbsolute'>User not found.</p>";
            } else {


                while ($row = $data->fetch_assoc()) {
                    $id = $row["ID_user"];
                    if ($_SESSION["id"] == $id) {

                        header("Location: index.php?page=publicGallery");
                    } else {
                        header("Location: index.php?page=otherPublicGallery&id=$id");
                    }
                }
            }


        }

    }

}

function uploadFile($isPublic)
{

    if (isset($_POST["submit"])) {


        $target_dir = "./user_galleries/" . $_SESSION["id"] . "/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check) {
            $uploadOk = 1;
        } else {
            echo "Sorry, the file is way too large.";
            $uploadOk = 0;
            return;
        }


// Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {

            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
        } else {

            if (($id = uploadImageToDtb($isPublic, $imageFileType)) > 0) {

                $target_file = $target_dir . $id . "." . $imageFileType;

            } else {
                echo "Sorry, there was an error uploading your file.";
                return;
            }


            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

function uploadImageToDtb($isPublic, $imageFileType)
{
    $added = date('Y-m-d H:i:s');
    $desc = " ";
    $userID = $_SESSION["id"];

    $db = connect();

    $sql = "INSERT INTO image (added,description, User_ID_user, isPublic, fileType) VALUES (?,?,?,?,?);";

    if ($stmt = $db->prepare($sql)) {
        $imageFileType = "." . $imageFileType;
        $stmt->bind_param("ssiis", $added, $desc, $userID, $isPublic, $imageFileType);
        $stmt->execute();
        $id = $stmt->insert_id;
        return $id;

    }
}

function loadOtherGallery($isPublic, $id)
{
    $db = connect();


    $sql = "SELECT * FROM image WHERE User_ID_user=$id AND isPublic=$isPublic";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $picId = $row["ID_image"];
            $imageFileType = $row["fileType"];

            $path = "./user_galleries/$id/$picId" . "$imageFileType";

            echo "<a href='?page=otherPictureDetail&id=$id&picId=$picId&fileType=$imageFileType'> <img src='$path' id='galleryPicture'></a>  ";

        }
    } else {
        return;
    }

}

function loadGallery($isPublic)
{
    $db = connect();
    $id = $_SESSION["id"];


    $sql = "SELECT * FROM image WHERE User_ID_user=$id AND isPublic=$isPublic";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $picId = $row["ID_image"];
            $imageFileType = $row["fileType"];

            $path = "./user_galleries/$id/$picId" . "$imageFileType";

            echo "<a href='?page=pictureDetail&id=$picId&fileType=$imageFileType'> <img src='$path' id='galleryPicture'></a>  ";

        }
    } else {
        return;
    }

}

function loadOtherImage($picId, $imageFileType, $usrId)
{

    echo "<img src= './user_galleries/$usrId/$picId" . "$imageFileType''>";

}

function loadImage($picId, $imageFileType)
{
    $usrId = $_SESSION["id"];
    echo "<img src= './user_galleries/$usrId/$picId" . "$imageFileType''>";
}

function loadLikes($picId)
{
    $db = connect();
    $sql = "SELECT * FROM approve WHERE FK_Image=$picId";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        $counter = 0;
        while ($row = $result->fetch_assoc()) {

            if ($row["approves"]) {
                $counter++;
            } else {
                $counter--;
            }
        }
        echo "<p> $counter </p>";
    } else {
        echo "<p> 0 </p>";
    }

}

function loadDescription($picId, $returnText)
{
    $db = connect();
    $sql = "SELECT * FROM image WHERE ID_image=$picId";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row

        while ($row = $result->fetch_assoc()) {

            if ($returnText) {
                $desc = $row["description"];
                echo " $desc";
                return;
            } else {
                $desc = $row["description"];
                $date = $row["added"];
                echo "<p>Added: $date</p>";
                echo "<p>$desc</p>";
            }

        }


    } else {
        echo "<p>  </p>";
    }

}

function postDescription($picId)
{
    if (isset($_POST["submitDescription"])) {
        $db = connect();
        $sql = "UPDATE image SET description=? WHERE ID_image=? ";

        if ($stmt = $db->prepare($sql)) {
            $text = $_POST["description"];

            $stmt->bind_param("si", $text, $picId);
            $stmt->execute();

            header("Refresh:0");
        }
    }

}

function loadComments($picId)
{
    $db = connect();
    $sql = "SELECT * FROM comment WHERE FK_Image=$picId";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row

        while ($row = $result->fetch_assoc()) {
            $text = $row["text"];
            $date = $row["added"];
            $commenter = $row["FK_Commenter"];

            $sql2 = "SELECT * FROM user WHERE ID_user=$commenter";
            $result2 = $db->query($sql2);
            if ($result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc();
                $commenterName = $row2["username"];

            }

            echo "<p>Added: $date <br>
                    $commenterName <br>
                    $text <br></p>";
        }

    } else {
        echo "<p>No comments available</p>";
    }

}


function postComment($picId)
{

    if (isset($_POST["submitComment"]) && isset($_SESSION["login"])) {
        $db = connect();
        $sql = "INSERT INTO comment (FK_Commenter, FK_Image, text, added) VALUES (?,?,?,?);";

        if ($stmt = $db->prepare($sql)) {
            $id = $_SESSION["id"];
            $text = $_POST["comment"];
            $date = date('Y-m-d H:i:s');

            $stmt->bind_param("iiss", $id, $picId, $text, $date);
            $stmt->execute();


        }
    }
}

function setApprove($picId)
{


    if (isset($_POST["unlike"])) {

        $point = 0;
    } else if (isset($_POST["like"])) {

        $point = 1;
    } else return;

    if (isset($_SESSION["login"])) {

    } else return;

    //check if already approved with this score
    $id = $_SESSION["id"];
    $db = connect();
    $sql = "SELECT * FROM approve WHERE FK_Image=$picId && FK_Approver=$id";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row

        $row = $result->fetch_assoc();
        $score = $row["approves"];
        if ($score == $point) {

            return;
        } else {

            setApproveUpdate($point, $picId, $id);
        }

    } else setApproveInsert($point, $picId, $id);
    //no previous approves


}

function setApproveUpdate($point, $picId, $id)
{

    $db = connect();
    $sql = "UPDATE approve SET approves=? WHERE FK_Image=? && FK_Approver=? ";

    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("iii", $point, $picId, $id);
        $stmt->execute();

        header("Refresh:0");
    }

}

function setApproveInsert($point, $picId, $id)
{

    $db = connect();
    $sql = "INSERT INTO approve (FK_Approver,FK_Image,approves)VALUES(?,?,?)";

    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("iii", $id, $picId, $point);
        $stmt->execute();

        header("Refresh:0");
    }
}

function isAlly($loggedUser, $otherUser)
{

    $db = connect();
    $condition = -1;

    $sql = "SELECT * FROM user_aliances WHERE ID_user_from=$loggedUser && ID_user_to=$otherUser";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {

        $condition++;
    } else return false;


    $sql = "SELECT * FROM user_aliances WHERE ID_user_from=$otherUser && ID_user_to=$loggedUser";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {

        $condition++;
    } else return false;
    if ($condition == 1) {
        return true;
    } else {
        return false;
    }

}

function sendRequest()
{
    if (isset($_SESSION["login"]) && isset($_GET["allyRequest"])) {
        $db = connect();
        $sendTo = $_GET["id"];
        $sendFrom = $_SESSION["id"];

        $sql = "INSERT INTO user_aliances (ID_user_from, ID_user_to) VALUES (?,?)";
        if ($stmt = $db->prepare($sql)) {

            $stmt->bind_param("ii", $sendFrom, $sendTo);
            $stmt->execute();

            header("Refresh:0");
        }
    }
}

function denyRequest()
{
    if (isset($_SESSION["login"]) && isset($_GET["allyRequest"])) {
        $db = connect();
        $sendTo = $_SESSION["id"];
        $sendFrom = $_GET["id"];

        $sql = "DELETE FROM user_aliances WHERE ID_user_from=? && ID_user_to=?";
        if ($stmt = $db->prepare($sql)) {

            $stmt->bind_param("ii", $sendFrom, $sendTo);
            $stmt->execute();

            header("Refresh:0");
        }
    }
}

function loadRequests()
{
    if (isset($_SESSION["login"])) {
        $id = $_SESSION["id"];

        $db = connect();

        $sql = "SELECT * FROM user_aliances WHERE ID_user_to=$id";

        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row

            while ($row = $result->fetch_assoc()) {
                $fromId = $row["ID_user_from"];
                if (!isAlly($id, $fromId)) {

                    $sql2 = "SELECT * FROM user WHERE ID_user=$fromId";
                    $result2 = $db->query($sql2);
                    if ($result2->num_rows > 0) {
                        $row2 = $result2->fetch_assoc();
                        $uname = $row2["username"];

                        $pathAcc = $_SERVER["REQUEST_URI"] . "&allyRequest=1&id=$fromId";
                        $pathDec = $_SERVER["REQUEST_URI"] . "&allyRequest=0&id=$fromId";
                        echo "<p>Ally request from: $uname <a href='$pathAcc'>Accept</a> <a href='$pathDec'>Decline</a></p>";
                    }
                }
            }

        } else {
            echo "<p>No requests available</p>";
        }
    }
}

function getUsername($id)
{
    $db = connect();

    $sql = "SELECT * FROM user WHERE ID_user=$id";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["username"];
    }
}

function loadAllies()
{
    if (isset($_SESSION["login"])) {
        $id = $_SESSION["id"];

        $db = connect();

        $sql = "SELECT * FROM user_aliances WHERE ID_user_to=$id";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $idFrom = $row["ID_user_from"];

                $sql2 = "SELECT * FROM user_aliances WHERE ID_user_to=$idFrom &&ID_user_from=$id";
                $result2 = $db->query($sql2);
                if ($result2->num_rows > 0) {

                    $pathBreak = $_SERVER["REQUEST_URI"] . "&allyRequest=3&id=$idFrom";
                    $uname = getUsername($idFrom);
                    echo "<p>Alliance with: $uname  <a href='$pathBreak'>Break</a></p>";


                }
            }
        } else {
            echo "<p>No alliances found.</p>";
        }
    }

}

function pendingRequest()
{
    $from = $_SESSION["id"];
    $to = $_GET["id"];

    $db = connect();
    $sql = "SELECT * FROM user_aliances WHERE ID_user_to=$to && ID_user_from=$from";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        return true;

    } else return false;
}

function breakAlliance()
{
    if (isset($_SESSION["login"]) && isset($_GET["allyRequest"])) {
        $db = connect();
        $myId = $_SESSION["id"];
        $id = $_GET["id"];

        $sql = "DELETE FROM user_aliances WHERE ID_user_from=? && ID_user_to=?";
        if ($stmt = $db->prepare($sql)) {

            $stmt->bind_param("ii", $myId, $id);
            $stmt->execute();


        }
        $sql2 = "DELETE FROM user_aliances WHERE ID_user_from=? && ID_user_to=?";
        if ($stmt2 = $db->prepare($sql2)) {

            $stmt2->bind_param("ii", $id, $myId);
            $stmt2->execute();

            header("Refresh:0");
        }

    }
}

function loadUsers()
{

    if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {


        $db = connect();

        $sql = "SELECT * FROM user WHERE isAdmin!=1";
        $result = $db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $uname = $row["username"];
                $date = $row["created"];
                $id = $row["ID_user"];

                $pathDelete = $_SERVER["REQUEST_URI"] . "&manageRequest=1&id=$id";
                echo "<p>Username: $uname, created: $date  <a href='$pathDelete'>Delete user</a></p>";


            }

        } else {
            echo "<p>No users found.</p>";
        }
    }
}

function deleteUser($userId)
{

    deleteAllComments($userId);
    deleteAllApproves($userId);
    deleteAllAlliances($userId);
    deleteAllImages($userId);

    $db = connect();


    $path = "./user_galleries/" . $userId . "/";
    rmdir($path);

    $sql = "DELETE FROM user WHERE ID_user=? ";
    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        header("Refresh:0");
    }

}

function deleteAllImages($userId)
{
    $db = connect();
    $sql = "SELECT * FROM image WHERE User_ID_user=$userId";

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imageId = $row["ID_image"];

            deleteImage($imageId);
        }
    }
}

function deleteApprovesCommentsOnImage($imageId)
{
    $db = connect();
    $sql = "SELECT * FROM comment WHERE FK_Image=$imageId";

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $commentId = $row["ID_Comment"];

            deleteComment($commentId);
        }
    }

    $sql1 = "SELECT * FROM approve WHERE FK_Image=$imageId";

    $result1 = $db->query($sql1);
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_assoc()) {
            $approverId = $row1["FK_Approver"];

            deleteApprove($approverId, $imageId);
        }
    }


}

function deleteImage($imageId)
{
    deleteApprovesCommentsOnImage($imageId);

    $db = connect();

    $sql2 = "SELECT * FROM image WHERE ID_image=$imageId";
    $result2 = $db->query($sql2);
    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_assoc()) {
            $userId = $row2["User_ID_user"];
            $fileType = $row2["fileType"];


        }
    }


    $path = "./user_galleries/" . $userId . "/" . $imageId . $fileType;
    unlink($path);

    $sql = "DELETE FROM image WHERE ID_image=? ";
    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("i", $imageId);
        $stmt->execute();

        header("Refresh:0");
    }
}

function deleteAllComments($userId)
{

    $db = connect();
    $sql = "SELECT * FROM comment WHERE FK_Commenter=$userId";

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $commentId = $row["ID_Comment"];

            deleteComment($commentId);
        }
    }
}

function deleteComment($commentId)
{
    $db = connect();

    $sql = "DELETE FROM comment WHERE ID_Comment=? ";
    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("i", $commentId);
        $stmt->execute();

        header("Refresh:0");
    }

}


function deleteAllApproves($userId)
{

    $db = connect();
    $sql = "SELECT * FROM approve WHERE FK_Approver=$userId";

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imageId = $row["FK_Image"];

            deleteApprove($userId, $imageId);
        }
    }

}

function deleteApprove($userId, $imageId)
{
    $db = connect();

    $sql = "DELETE FROM approve WHERE FK_Image=? &&  FK_Approver=?";
    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("ii", $imageId, $userId);
        $stmt->execute();

        header("Refresh:0");
    }
}

function deleteAllAlliances($userId)
{
    $db = connect();
    $sql = "SELECT * FROM user_aliances WHERE ID_user_from=$userId";

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $otherUser = $row["ID_user_to"];

            deleteAlliance($userId, $otherUser);
        }
    }

    $sql2 = "SELECT * FROM user_aliances WHERE ID_user_to=$userId";

    $result2 = $db->query($sql2);
    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_assoc()) {
            $otherUser = $row2["ID_user_from"];

            deleteAlliance($userId, $otherUser);
        }
    }
}

function deleteAlliance($userId, $otherUserId)
{
    $db = connect();


    $sql = "DELETE FROM user_aliances WHERE ID_user_to=? &&  ID_user_from=?";
    if ($stmt = $db->prepare($sql)) {

        $stmt->bind_param("ii", $userId, $otherUserId);
        $stmt->execute();


    }

    $sql2 = "DELETE FROM user_aliances WHERE ID_user_to=? &&  ID_user_from=?";
    if ($stmt2 = $db->prepare($sql2)) {

        $stmt2->bind_param("ii", $otherUserId, $userId);
        $stmt2->execute();

        header("Refresh:0");
    }

}

function commentsToJson($imageId)
{
    echo "fnaofaoiafinofa";

    $db = connect();
    $sql = "SELECT * FROM comment JOIN user ON FK_Commenter=ID_user WHERE FK_Image=$imageId";

    $response = array();
    $posts = array();

    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uname = $row["username"];
            $added = $row["added"];
            $text = $row["text"];

            $posts[] = array('uname' => $uname, 'added' => $added, 'text' => $text);
        }
    } else return;
    $response['posts'] = $posts;

    $path = "./export_JSON/results.json";

    $fp = fopen($path, 'w');
    fwrite($fp, json_encode($response));
    fclose($fp);

    if (file_exists($path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        ob_clean();
        flush();
        readfile($path);
        unlink($path);
        exit;
    }


}

function importUsers()
{
    $db = connect();


    if (isset($_POST["submitJson"])) {


        $target_dir = "./import_JSON/";


        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $jsonFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Allow certain file formats
        if ($jsonFileType != "json") {

            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
        } else {

            $target_file = $target_dir . "input." . $jsonFileType;
        }


        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $jsonData = file_get_contents($target_file);
            $obj = json_decode($jsonData, true);

            foreach ($obj as $array => $v) {
                $uname = $v["uname"];
                $psw = $v["psw"];
                $isAdmin = $v["isAdmin"];
                $date = date('Y-m-d H:i:s');

                $sql = "INSERT INTO user (username, password, created,isAdmin) VALUES (?,?,?,?)";
                if ($stmt = $db->prepare($sql)) {
                    $stmt->bind_param("sssi", $uname, $psw, $date, $isAdmin);
                    $stmt->execute();
                }
            }


            echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


}


?>