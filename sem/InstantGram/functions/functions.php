<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 01.02.2019
 * Time: 21:07
 */

function connect(){
    $db= new mysqli("localhost","root","","instantgramdb");
    if($db->errno>0)
        die("something broke");
    $db->set_charset("utf8");
    return $db;
}

function register(){
    $db = connect();

    if(isset($_POST["btnSubmitRegistered"])){
        if(empty($_POST["uname"]) || empty($_POST["psw"]) || empty($_POST["repsw"]) ){


            echo "<p class='warning'>Fill the form</p>";

        } else if ($_POST["psw"] != $_POST["repsw"]) {
        echo "<p class='warning'>Passwords do not match.</p>";
    } else {
            $uname = $_POST["uname"];
            $psw = password_hash($_POST["psw"], PASSWORD_BCRYPT);
            $date = date('Y-m-d H:i:s');
            $isAdmin=0;
            $error = 0;

            $sql = "SELECT * FROM user WHERE username='" . $uname . "'";

            if($data=$db->query($sql)){
                if($data->num_rows>0){
                    $error=1;
                }

            }

            if($error==1){
                echo "<p class='warning'>This username is already taken.</p>";
            }else{

                $sql5 = "INSERT INTO user (username, password, created, isAdmin) VALUES (?,?,?,?);";

                if($stmt=$db->prepare($sql5)){

                    $stmt->bind_param("sssi",$uname,$psw,$date,$isAdmin);
                $stmt->execute();
                $id=$stmt->insert_id;

                    echo "<p class='warning'>Registration complete.</p>";

                }
            }
        }
    }
}

function login(){
    $db=connect();
    if(!empty($_POST["uname"]) && !empty($_POST["psw"])){
        $uname=$_POST["uname"];
        $psw=$_POST["psw"];

        $stmt=$db->prepare("SELECT * FROM user WHERE username=?");
        $stmt->bind_param("s",$uname);
        $stmt->execute();
        $result=$stmt->get_result();
        $line=$result->fetch_assoc();



        if(!empty($line)&&password_verify($psw,$line["password"])){

            $_SESSION["login"]=$uname;
            $_SESSION["id"]=$line["ID_user"];
            $_SESSION["isAdmin"]=$line["isAdmin"];

            header("Location: index.php?page=publicGallery");
        }else{
            echo "<p class='warning'>Wrong login, try again.</p>";
        }

    }

}
function changeCredentials(){
    $db=connect();
    if(!empty($_POST["uname"])&&$_POST["uname"]!=$_SESSION["login"]){
        $uname=$_POST["uname"];
        $id=$_SESSION["id"];

        $sql = "SELECT * FROM user WHERE username='" . $uname . "'";

        if($data=$db->query($sql)){
            if($data->num_rows>0){
                echo "<p class='warning'>Username already in use.</p>";
            }else{

                $stmt=$db->prepare("UPDATE user SET username=? WHERE ID_user=?");
                $stmt->bind_param("si",$uname,$id);
                $stmt->execute();
                $_SESSION["login"]=$uname;
                header("Refresh:0");

            }

        }




    }

    if(!empty($_POST["psw"])){
        $psw=password_hash($_POST["psw"], PASSWORD_BCRYPT);
        $id=$_SESSION["id"];
        $stmt=$db->prepare("UPDATE user SET password=? WHERE ID_user=?");
        $stmt->bind_param("si",$psw,$id);
        $stmt->execute();

    }




}


function isLogged(){
    @session_start();

    if(isset($_SESSION["login"])){
        return 1;
    }
    return 0;

}
function logout(){
    @session_destroy();
    header("Location: index.php");
}


function chooseHeader(){
if (isLogged()) {

    include "header_2.php";
} else {
    include "header_1.php";
    }
}
function indexRedirect(){

    chooseHeader();

    if(isset($_GET["page"])) {
        if($_GET["page"]=="login"||$_GET["page"]=="register"){
            include "hero.php";
        }

        $file = "./" . $_GET["page"] . ".php";

    }else {
        include "hero.php";
    }
    if(isset($file)&&file_exists($file)){
        include $file;
    }
}

function findUser(){


}




?>