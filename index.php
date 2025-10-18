<?php
$dadosPath = __DIR__ . '/dados/dados.json';
if (!file_exists($dadosPath)) {
    file_put_contents($dadosPath, json_encode([]));
}

$apks = json_decode(file_get_contents($dadosPath), true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Valte Streaming - Loja de Apps</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to bottom, #fff7e6, #ffe0b3);
    color: #333;
    margin: 0;
    padding: 0;
}
header {
    text-align: center;
    padding: 25px;
    background: linear-gradient(90deg, #ff9a76, #ffd27f);
    font-size: 2em;
    font-weight: bold;
    color: #000;
    text-shadow: 1px 1px 3px #fff5;
}
.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    padding: 20px;
}
.card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 15px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 140, 0, 0.5);
}
.card img {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    margin-bottom: 10px;
    object-fit: cover;
    border: 2px solid rgba(0,0,0,0.1);
}
.card h3 {
    font-size: 1.2em;
    margin: 5px 0;
    color: #ff7f50;
}
a.download {
    background: linear-gradient(90deg, #ff9a76, #ffd27f);
    color: #000;
    font-weight: bold;
    padding: 8px 15px;
    border-radius: 10px;
    text-decoration: none;
    display: inline-block;
    margin-top: 10px;
    transition: background 0.3s, transform 0.2s;
}
a.download:hover {
    background: linear-gradient(90deg, #ffd27f, #ff9a76);
    transform: scale(1.05);
}
footer {
    text-align: center;
    padding: 15px;
    color: #555;
    font-size: 0.9em;
    border-top: 1px solid rgba(0,0,0,0.1);
}

/* Responsividade para celular */
@media (max-width:600px){
    .container {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .card img { width: 80px; height: 80px; }
}
</style>
</head>
<body>
<header>🎬 Valte Streaming - Loja de Apps</header>

<div class="container">
<?php if (!empty($apks)): ?>
    <?php foreach ($apks as $apk): ?>
        <div class="card">
            <img src="<?= htmlspecialchars($apk['icone']) ?>" alt="Ícone">
            <h3><?= htmlspecialchars($apk['nome']) ?></h3>
            <a class="download" href="<?= htmlspecialchars($apk['arquivo']) ?>" download>📥 Baixar</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center; font-size:1.1em; margin-top:20px;">Nenhum aplicativo disponível no momento.</p>
<?php endif; ?>
</div>

<footer>© <?= date('Y') ?> Valte Streaming</footer>
</body>
</html>
