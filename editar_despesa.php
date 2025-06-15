<?php
session_start();
require_once 'config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o ID da despesa foi fornecido
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$despesa_id = $_GET['id'];
$erro = "";

// Buscar dados da despesa
$consulta = "SELECT * FROM despesas WHERE id = $despesa_id AND usuario_id = " . $_SESSION['usuario_id'];
$resultado = mysqli_query($conexao, $consulta);
$despesa = mysqli_fetch_assoc($resultado);

if (!$despesa) {
    header("Location: dashboard.php");
    exit();
}

// Processar o formulário de edição
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $valor = str_replace(',', '.', $_POST['valor']);
    $data_despesa = $_POST['data_despesa'];

    if (empty($titulo) || empty($valor) || empty($data_despesa)) {
        $erro = "Título, valor e data são obrigatórios";
    } else {
        $consulta = "UPDATE despesas SET 
                    titulo = '$titulo', 
                    descricao = '$descricao', 
                    valor = '$valor', 
                    data_despesa = '$data_despesa' 
                    WHERE id = $despesa_id AND usuario_id = " . $_SESSION['usuario_id'];

        if (mysqli_query($conexao, $consulta)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $erro = "Erro ao atualizar despesa: " . mysqli_error($conexao);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Despesa - Sistema de Despesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistema de Despesas</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">Olá, <?php echo $_SESSION['usuario_nome']; ?></span>
                <a class="nav-link" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Editar Despesa</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger">
                                <?php echo $erro; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo $despesa['titulo']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo $despesa['descricao']; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="valor" name="valor" value="<?php echo number_format($despesa['valor'], 2, ',', '.'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="data_despesa" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data_despesa" name="data_despesa" value="<?php echo $despesa['data_despesa']; ?>" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>