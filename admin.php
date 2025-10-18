<?php
$pin = "1212";
session_start();

// Login simples por PIN
if (isset($_POST['pin'])) {
    if ($_POST['pin'] === $pin) {
        $_SESSION['logado'] = true;
    } else {
        echo "<script>alert('PIN incorreto!');</script>";
    }
}

if (!isset($_SESSION['logado'])) {
?>
<form method="POST" style="text-align:center; margin-top:100px;">
    <h2>🔒 Painel Admin</h2>
    <input type="password" name="pin" placeholder="Digite o PIN" required>
    <button type="submit">Entrar</button>
</form>
<style>
body { background-color:#000; color:#fff; font-family:Arial; }
input, button { padding:10px; border-radius:6px; border:none; }
button { background:red; color:white; cursor:pointer; }
</style>
<?php
    exit;
}

// Caminhos e criação automática
$dadosDir = __DIR__ . '/dados';
$uploadDir = __DIR__ . '/upload';
if (!file_exists($dadosDir)) mkdir($dadosDir, 0777, true);
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

$dadosPath = $dadosDir . '/dados.json';
if (!file_exists($dadosPath)) file_put_contents($dadosPath, json_encode([]));

$apks = json_decode(file_get_contents($dadosPath), true);

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    if ($acao === 'add') {
        $nome = trim($_POST['nome']);
        $icone = $_POST['icone'] ?: '';
        $arquivo = $_POST['arquivo'] ?: '';

        if (!empty($_FILES['iconeUpload']['name'])) {
            $iconePath = 'upload/' . basename($_FILES['iconeUpload']['name']);
            move_uploaded_file($_FILES['iconeUpload']['tmp_name'], $iconePath);
            $icone = $iconePath;
        }

        if (!empty($_FILES['apkUpload']['name'])) {
            $apkPath = 'upload/' . basename($_FILES['apkUpload']['name']);
            move_uploaded_file($_FILES['apkUpload']['tmp_name'], $apkPath);
            $arquivo = $apkPath;
        }

        $novoApk = [
            'nome' => $nome,
            'icone' => $icone,
            'arquivo' => $arquivo
        ];

        // Adiciona no topo da lista (em destaque)
        array_unshift($apks, $novoApk);
        file_put_contents($dadosPath, json_encode($apks, JSON_PRETTY_PRINT));

        // Evita duplicação ao atualizar a página
        header("Location: admin.php?ok=1");
        exit;
    }

    if ($acao === 'delete') {
        $index = $_POST['index'];
        
        // Apaga arquivos físicos se existirem
        if (!empty($apks[$index]['icone']) && file_exists($apks[$index]['icone'])) {
            unlink($apks[$index]['icone']);
        }
        if (!empty($apks[$index]['arquivo']) && file_exists($apks[$index]['arquivo'])) {
            unlink($apks[$index]['arquivo']);
        }

        // Remove do array
        array_splice($apks, $index, 1);
        file_put_contents($dadosPath, json_encode($apks, JSON_PRETTY_PRINT));
        header("Location: admin.php?deleted=1");
        exit;
    }

    if ($acao === 'edit') {
        $i = $_POST['index'];
        $apks[$i]['nome'] = trim($_POST['nome']);

        if (!empty($_FILES['iconeUpload']['name'])) {
            $iconePath = 'upload/' . basename($_FILES['iconeUpload']['name']);
            move_uploaded_file($_FILES['iconeUpload']['tmp_name'], $iconePath);
            $apks[$i]['icone'] = $iconePath;
        } elseif (!empty($_POST['icone'])) {
            $apks[$i]['icone'] = $_POST['icone'];
        }

        if (!empty($_FILES['apkUpload']['name'])) {
            $apkPath = 'upload/' . basename($_FILES['apkUpload']['name']);
            move_uploaded_file($_FILES['apkUpload']['tmp_name'], $apkPath);
            $apks[$i]['arquivo'] = $apkPath;
        } elseif (!empty($_POST['arquivo'])) {
            $apks[$i]['arquivo'] = $_POST['arquivo'];
        }

        file_put_contents($dadosPath, json_encode($apks, JSON_PRETTY_PRINT));
        header("Location: admin.php?edited=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Valte Streaming</title>
<style>
body { background:#000; color:#fff; font-family:Arial; padding:20px; }
h2 { text-align:center; color:red; }
form { background:#111; padding:15px; border-radius:10px; margin-bottom:20px; }
input { width:100%; padding:8px; margin:5px 0; border-radius:6px; border:none; }
button { background:red; color:#fff; border:none; padding:8px 12px; border-radius:8px; cursor:pointer; }
table { width:100%; border-collapse:collapse; color:#fff; }
th, td { padding:8px; text-align:left; border-bottom:1px solid #333; }
img { width:40px; height:40px; border-radius:8px; }
.edit-box { background:#222; padding:10px; margin-top:10px; border-radius:10px; }
@media (max-width:600px){
    body { padding:10px; }
    table, tr, td, th { display:block; width:100%; }
    td, th { border:none; }
}
</style>
</head>
<body>
<h2>🎬 Painel Admin - Valte Streaming</h2>

<?php if (isset($_GET['ok'])) echo "<p style='color:lightgreen;'>✅ APK adicionado com sucesso!</p>"; ?>
<?php if (isset($_GET['edited'])) echo "<p style='color:lightblue;'>✏️ APK editado com sucesso!</p>"; ?>
<?php if (isset($_GET['deleted'])) echo "<p style='color:orange;'>🗑️ APK excluído com sucesso!</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="acao" value="add">
    <label>Nome do APK:</label>
    <input type="text" name="nome" required>
    <label>Ícone (upload ou link):</label>
    <input type="file" name="iconeUpload">
    <input type="text" name="icone" placeholder="ou link direto">
    <label>Arquivo APK (upload ou link):</label>
    <input type="file" name="apkUpload">
    <input type="text" name="arquivo" placeholder="ou link direto">
    <button type="submit">Adicionar APK</button>
</form>

<h3>📦 Aplicativos Cadastrados</h3>
<table>
<tr><th>Ícone</th><th>Nome</th><th>Ações</th></tr>
<?php foreach ($apks as $i => $apk): ?>
<tr>
    <td><img src="<?= htmlspecialchars($apk['icone']) ?>"></td>
    <td><?= htmlspecialchars($apk['nome']) ?></td>
    <td>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="acao" value="delete">
            <input type="hidden" name="index" value="<?= $i ?>">
            <button type="submit" onclick="return confirm('Excluir este APK?')">🗑️</button>
        </form>
        <button onclick="document.getElementById('edit<?= $i ?>').style.display='block'">✏️</button>
    </td>
</tr>
<tr id="edit<?= $i ?>" style="display:none;">
<td colspan="3">
<div class="edit-box">
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="acao" value="edit">
    <input type="hidden" name="index" value="<?= $i ?>">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($apk['nome']) ?>">
    <label>Novo Ícone (upload ou link):</label>
    <input type="file" name="iconeUpload">
    <input type="text" name="icone" placeholder="ou link direto">
    <label>Novo APK (upload ou link):</label>
    <input type="file" name="apkUpload">
    <input type="text" name="arquivo" placeholder="ou link direto">
    <button type="submit">Salvar</button>
    <button type="button" onclick="document.getElementById('edit<?= $i ?>').style.display='none'">Fechar</button>
</form>
</div>
</td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
