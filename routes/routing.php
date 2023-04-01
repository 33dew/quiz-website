<?php


use Helper\Functions;
use Route\Route;
use Session\Session;
use Database\DB;
use Google\Auth;

Route::get("/elo/{siema}", function($req) {
    echo $req->uri("siema");
//    $req->get("siema");
//    $req->post("siema");
});

Route::get("/google-login", function($req){
    $code = $req->get("code");
    if($code){
        $token = Auth::$client->fetchAccessTokenWithAuthCode($code);
        if(!isset($token["error"])){
            Auth::$client->setAccessToken($token['access_token']);
            $google_oauth = new Google_Service_Oauth2(Auth::$client);
            $google_account_info = $google_oauth->userinfo->get();
            $checkIfUserHaveNormalAccount = DB::query("SELECT * FROM uzytkownicy WHERE `email` = ? AND `google` = ?", [$google_account_info->email, 0]);
            if(count($checkIfUserHaveNormalAccount) > 0) {
                Functions::redirect("/login");
            }
            $checkIfUserExists = DB::query("SELECT * FROM uzytkownicy WHERE `google` = ? AND `email` = ?", [1, $google_account_info->email]);
            if(count($checkIfUserExists) == 0){
                DB::query("INSERT INTO uzytkownicy VALUES (?, ?, ?, ?, ?)", [NULL, $google_account_info->name, NULL, $google_account_info->email, 1]);
            }
            Session::set("user", [
                "id" => $google_account_info->id,
                "name" => $google_account_info->name,
                "email" => $google_account_info->email,
                "picture" => $google_account_info->picture
            ]);
            Session::set("loggedIn", true);
            Functions::redirect("/");
        } else {
            Functions::redirect("/google-login");
        }
    } else {
        $auth_url = Auth::getAuthLink();
        Functions::redirect($auth_url);
    }
});

Route::get("/exam/{examId}", function($req) {
    $examId =  $req->uri("examId");
    $questions = DB::query("SELECT nazwa, json, time FROM egzaminy WHERE `id` = ?", [$examId]);
    if(count($questions) == 0) return Functions::redirect("/");
    return Functions::view("exam", [
        "exam" => $questions[0]['nazwa'],
        "questions" => $questions[0]['json'],
        "title" => "Egzamin ".$questions[0]['nazwa'],
        "examId" => $examId,
        "time" => $questions[0]['time']
    ]);
});

Route::post("/exam/{examId}/finish", function($req) {
    $examId =  $req->uri("examId");
    $json = $req->post("answers");
    $result = $req->post("result");
    DB::query("INSERT INTO `wyniki` VALUES (NULL, ?, ?, ?, ?);", [$examId, $result, $json, Session::get('userId')]);
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
    $checker = DB::query("SELECT COUNT(*), id, google FROM `uzytkownicy` WHERE `login` = ? AND `haslo` = ?", [$login, $pass]);
    if($checker[0][2] == 1){
        return Functions::view("login", [
            "error" => "Masz konto przez google juz!",
            "title" => "Logowanie"
        ]);
    }
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
    $mail = $req->post("mail");
    $checker = DB::query("SELECT COUNT(*), id FROM `uzytkownicy` WHERE `login` = ?", [$login]);
    if($checker[0][0] == 1){
        return Functions::view("register", [
            "error" => "Ten login jest juz w uzyciu!"
        ]);
    }
    $checkEmail = DB::query("SELECT COUNT(*) FROM `uzytkownicy` WHERE `email` = ?", [$mail]);
    if($checkEmail[0][0] == 1){
        return Functions::view("register", [
            "error" => "Ten email jest juz w uzyciu!"
        ]);
    }
    DB::query("INSERT INTO `uzytkownicy` VALUES (NULL, ?, ?, ?, ?)", [$login, $pass, $mail, NULL]);
    Session::set("loggedIn", true);
    Session::set("userId", $checker[0][1]);
    return Functions::redirect("/");
});

Route::get("/logout", function($req) {
    if(Session::get("loggedIn")){
        Session::delete("loggedIn");
    }
    if(Session::get("user")){
        Session::delete("user");
    }
    return Functions::redirect("/");
});