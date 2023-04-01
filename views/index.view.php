<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Egzaminy zawodowe</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto my-2 my-lg-0" style="--bs-scroll-height: 100px;">
                <?php if($loggedIn): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Egzaminy
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                                foreach($exams as $exam){
                                    echo '<li><a class="dropdown-item" href="/exam/'.$exam[0].'">'.$exam[1].'</a></li>';
                                }
                            ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <?php if($loggedIn): ?>
                    <a href="/logout" class="btn btn-outline-danger">Wyloguj</a>
                <?php else: ?>
                    <a href="/google-login" class="btn btn-outline-primary me-2">Google</a>
                    <a href="/login" class="btn btn-outline-primary me-2">Zaloguj</a>
                    <a href="/register" class="btn btn-outline-primary">Zarejestruj</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php if($loggedIn):
        echo '<div class="container-fluid p-5 d-flex gap-5 flex-wrap justify-content-center">';
        foreach($exams as $exam){
            ?>
            <div onclick="window.location.href = '/exam/<?= $exam['id'] ?>'" class="card-egz card rounded-3 overflow-hidden" style="width: 18rem;">
                <img src="<?= $exam['image'] ?>" class="card-img-top h-75" alt="...">
                <div class="card-body">
                    <p class="card-text"><?= $exam['nazwa'] ?></p>
                </div>
            </div>
<?php
        }
        echo '</div>';
    endif; 
?>