<?php
session_start();
include "db.php"; 

//* 1) AGGIUNTA PRODOTTO AL CARRELLO */


if (isset($_POST['add_to_cart'])) {
    $idProdotto = $_POST['id'];

    if (!isset($_SESSION['carrello'])) {
        $_SESSION['carrello'] = [];
    }

    $_SESSION['carrello'][] = $idProdotto;
}

/* 2) RECUPERA TUTTI I PRODOTTI */

$sqlProdotti = "SELECT id, nome, prezzo, immagine FROM prodotti;";
pg_prepare($db, "getAllProducts", $sqlProdotti);
$prodotti = pg_execute($db, "getAllProducts", array());

if (!$prodotti) {
    die("Errore query prodotti: " . pg_last_error($db));
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Neon Lanes</title>
<link rel="stylesheet" href="mainpage.css">
<link rel="icon" type="icon" href="resources/logo.png"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="script.js" defer></script>
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo">
        <h1>Neon Lanes</h1>
    </div>
    <div class="user"><h1>
        Ciao <?= htmlspecialchars($username)?>
        <i class="fa-solid fa-user" style="margin-left: 10px;"></i>
    </h1></div>
</header>

<main>

<div class="grid">

<?php
/* ============================
   3) MOSTRA I PRODOTTI
   ============================ */
while ($row = pg_fetch_assoc($prodotti)) {
?>
  <div class="prodotto">
    <img src="<?= htmlspecialchars($row['immagine']) ?>">
    <h3><?= htmlspecialchars($row['nome']) ?></h3>
    <p>€<?= htmlspecialchars($row['prezzo']) ?></p>

    <form method="POST">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <button type="submit" name="add_to_cart">Aggiungi al carrello</button>
    </form>
  </div>
<?php } ?>

</div>

<h2>Carrello</h2>

<ul>
<?php
/* 
   4) MOSTRA I PRODOTTI NEL CARRELLO */
   
if (!empty($_SESSION['carrello'])) {

    // Prepara query per SINGOLO prodotto
    $sqlSingolo = "SELECT nome, prezzo FROM prodotti WHERE id = $1;";
    pg_prepare($db, "getProductById", $sqlSingolo);

    foreach ($_SESSION['carrello'] as $id) {

        $res = pg_execute($db, "getProductById", array($id));

        if ($row = pg_fetch_assoc($res)) {
            echo "<li>" .
                 htmlspecialchars($row['nome']) .
                 " - €" .
                 htmlspecialchars($row['prezzo']) .
                 "</li>";
        }
    }

} else {
    echo "<li>Il carrello è vuoto</li>";
}
?>
</ul>

</main>

<footer>
    <div id="footer-box">
        <p>© 2026 - Neon Lanes - Bowlers_G21 - Pascariello Vincenzo, Pellecchia Simone, Turi Martina</p>
        <p id="data-ogg"></p>
    </div>
</footer>

</body>
</html>