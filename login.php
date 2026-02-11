<?php
session_start();

$old_nome = $_SESSION['old_data']['nome'] ?? '';
$old_email = $_SESSION['old_data']['email'] ?? '';
$old_user = $_SESSION['old_data']['username'] ?? '';
$error_signup = $_SESSION['error_signup'] ?? '';
$error_signin = $_SESSION['error_signin'] ?? '';
$success = $_SESSION['success'] ?? '';
$target_tab = $_SESSION['form_target'] ?? 'login';

unset($_SESSION['error_signup']);
unset($_SESSION['error_signin']);
unset($_SESSION['success']);
unset($_SESSION['old_data']);
unset($_SESSION['form_target']);
?>

<html>
<meta charset="UTF-8" />

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="login.css" />
    <link rel="icon" type="icon" href="resources/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="login.js" defer></script>
</head>

<body>
    <header>
        <div class="site" onclick="window.location.href='mainpage.php'">
            <img src="resources/logo.png" class="logo" alt="Logo">
            <h1>The Bowler Club</h1>
        </div>
    </header>
    <main>

        <!-- form di login -->
        <div class="signIn" style="display: <?php echo $target_tab == 'signup' ? 'none' : 'unset'; ?>">
            <h2>Effettua l'accesso</h2>

            <?php if (!empty($success)): ?>
            <div class="success-box">
            <i class="fa-solid fa-person-circle-check"></i> 
            <?php echo $success; ?>
            <i class="fa-solid fa-person-circle-check"></i> 
            </div>
            <?php endif; ?>

            <?php if (!empty($error_signin)): ?>
            <div class="error-box">
            <i class="fa-solid fa-triangle-exclamation"></i> 
            <?php echo $error_signin; ?>
            <i class="fa-solid fa-triangle-exclamation"></i> 
            </div>
            <?php endif; ?>

            <form id="signInForm" method="post" action="signin-manager.php">
                <div class="form">

                    <div class="input-row">
                        <input type="text" name="username" id="username" class="text" placeholder="Username"
                        value="<?php echo htmlspecialchars($old_user); ?>"/>
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <hr />
                    <div class="input-row">
                        <input type="password" name="password" id="password" class="text" placeholder="Password" />
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <hr />
                    <div class="submit">
                        <input type="submit" name="invia" value="Login" class="button" />
                    </div>
                </div>
            </form>
            <div class="switch">Nuovo utente?
                <input type="button" id="showSignUp" name="registrati" value="Registrati" class="button" />
            </div>
        </div>

        <!-- form di registrazione -->
        <div class="signUp" style="display: <?php echo $target_tab == 'signup' ? 'unset' : 'none'; ?>">
            <h2>Crea un account</h2>
            <form id="signUpForm" method="post" action="signup-manager.php">
                <div class="form">

                    <?php if (!empty($error_signup)): ?>
                        <div class="error-box">
                            <i class="fa-solid fa-triangle-exclamation"></i> 
                            <?php echo $error_signup; ?>
                            <i class="fa-solid fa-triangle-exclamation"></i> 
                        </div>
                        <hr />
                    <?php endif; ?>

                    <div class="input-row">
                        <input type="text" name="nome" id="nome" class="text" placeholder="Nome completo"
                            value="<?php echo htmlspecialchars($old_nome); ?>" />
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <hr />
                    <div class="input-row">
                        <input type="email" name="email" id="email" class="text" placeholder="Email"
                            value="<?php echo htmlspecialchars($old_email); ?>" />
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <hr />
                    <div class="input-row">
                        <input type="text" name="username" id="username" class="text" placeholder="Scegli Username"
                            value="<?php echo htmlspecialchars($old_user); ?>" />
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <hr />
                    <div class="input-row">
                        <input type="password" name="password" id="password" class="text" placeholder="Password" />
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <hr />
                    <div class="input-row">
                        <input type="password" name="conferma_password" id="conferma_password" class="text"
                            placeholder="Conferma Password" />
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <hr />
                    <div class="submit">
                        <input type="submit" name="registra" value="Registrati" class="button" />
                    </div>
                </div>
            </form>
            <div class="switch">Hai già un account?
                <input type="button" id="showSignIn" name="login" value="Accedi" class="button" />
            </div>
        </div>

        <img src="resources/logo.png" class="logo" />
    </main>
    <footer>
        <div id="footer-box">
            <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
        </div>
    </footer>
</body>
</html>