<div class="container-fluid d-flex min-vh-100 justify-content-center align-items-center">
    <form action="/register" method="POST">
        <div class="mb-3">
            <label for="loginInput" class="form-label">Login</label>
            <input name="login" required type="text" class="form-control" id="loginInput">
        </div>
        <div class="mb-3">
            <label for="passInput" class="form-label">Hasło</label>
            <input name="password" required type="password" class="form-control" id="passInput">
        </div>
        <div class="mb-3">
            <label for="emailInput" class="form-label">E-mail</label>
            <input name="mail" required type="email" class="form-control" id="emailInput" aria-describedby="error">
            <div id="error" class="form-text text-danger"><?php if(isset($error)) echo $error; ?></div>
        </div>
        <button type="submit" class="btn btn-primary">Zarejestruj</button>
    </form>
</div>