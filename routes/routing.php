<?php


use Helper\Functions;
use Route\Route;
use Session\Session;
use Database\DB;

Route::get("/elo/{siema}", function($req) {
    echo $req->uri("siema");
//    $req->get("siema");
//    $req->post("siema");
});

Route::get("/exam/{examId}", function($req) {
    $examId =  $req->uri("examId");
    $questions = DB::query("SELECT nazwa, json FROM egzaminy WHERE `id` = '$examId'");
    if(count($questions) == 0) return Functions::redirect("/");
    return Functions::view("exam", [
        "exam" => $questions[0]['nazwa'],
        "questions" => $questions[0]['json'],
        "title" => "Egzamin ".$questions[0]['nazwa'],
        "examId" => $examId
    ]);
});

Route::post("/exam/{examId}/finish", function($req) {
    $examId =  $req->uri("examId");
    $json = $req->post("answers");
    $result = $req->post("result");
    DB::query("INSERT INTO `wyniki` VALUES (NULL, '$examId', '$result', '$json', '".Session::get('userId')."');");
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "ok",
        "result" => $result,
        "json" => $json
    ]);
});

Route::get("/", function($req) {
    if(Session::get("loggedIn")) {
        $exams = DB::query("SELECT id, nazwa, image FROM egzaminy");
    } else {
        $exams = null;
    }
    return Functions::view("index", [
        "loggedIn" => Session::get("loggedIn"),
        "exams" => $exams,
        "title" => "Strona główna"
    ]);
});

Route::get("/login", function($req) {
    if(Session::get("loggedIn")){
        return Functions::redirect("/");
    }
    return Functions::view("login", [
        "title" => "Logowanie"
    ]);
});

Route::get("/register", function($req) {
    if(Session::get("loggedIn")){
        return Functions::redirect("/");
    }
    return Functions::view("register");
});

Route::post("/login", function($req) {
    $login = $req->post("login");
    $pass = $req->post("password");
    $checker = DB::query("SELECT COUNT(*), id FROM `uzytkownicy` WHERE `login` = '$login' AND `haslo` = '$pass'");
    if($checker[0][0] == 0){
        return Functions::view("login", [
            "error" => "Niepoprawne dane!",
            "title" => "Logowanie"
        ]);
    }
    Session::set("loggedIn", true);
    Session::set("userId", $checker[0][1]);
    Functions::redirect("/");
});

Route::post("/register", function($req) {
    $login = $req->post("login");
    $pass = $req->post("password");
    $name = $req->post("name");
    $secname = $req->post("secname");
    $mail = $req->post("mail");
    $pesel = $req->post("pesel");
    $checker = DB::query("SELECT COUNT(*), id FROM `uzytkownicy` WHERE `login` = '$login'");
    if($checker[0][0] == 1){
        return Functions::view("register", [
            "error" => "Ten login jest juz w uzyciu!"
        ]);
    }
    DB::query("INSERT INTO `uzytkownicy` VALUES (NULL, '$name', '$secname', '$login', '$pass', '$mail', '$pesel')");
    Session::set("loggedIn", true);
    Session::set("userId", $checker[0][1]);
    return Functions::redirect("/");
});

Route::get("/logout", function($req) {
    if(Session::get("loggedIn")){
        Session::delete("loggedIn");
    }
    return Functions::redirect("/");
});