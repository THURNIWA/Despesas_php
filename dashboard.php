<?php
session_start();
require_once 'config/database.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$mensagem = "";
$erro = "";

// Processar exclusão de despesa
if (isset($_POST['excluir_despesa'])) {
    $despesa_id = $_POST['despesa_id'];
    $consulta = "DELETE FROM despesas WHERE id = $despesa_id AND usuario_id = " . $_SESSION['usuario_id'];

    if (mysqli_query($conexao, $consulta)) {
        $mensagem = "Despesa excluída com sucesso!";
    } else {
        $erro = "Erro ao excluir despesa: " . mysqli_error($conexao);
    }
}

// Processar cadastro de nova despesa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar_despesa'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $valor = str_replace(',', '.', $_POST['valor']);
    $data_despesa = $_POST['data_despesa'];

    if (empty($titulo) || empty($valor) || empty($data_despesa)) {
        $erro = "Título, valor e data são obrigatórios";
    } else {
        $consulta = "INSERT INTO despesas (usuario_id, titulo, descricao, valor, data_despesa) 
                    VALUES (" . $_SESSION['usuario_id'] . ", '$titulo', '$descricao', '$valor', '$data_despesa')";

        if (mysqli_query($conexao, $consulta)) {
            $mensagem = "Despesa cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar despesa: " . mysqli_error($conexao);
        }
    }
}

// Buscar despesas do usuário
$consulta = "SELECT * FROM despesas WHERE usuario_id = " . $_SESSION['usuario_id'] . " ORDER BY data_despesa DESC";
$resultado = mysqli_query($conexao, $consulta);
$despesas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Despesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema de Despesas</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">Olá, <?php echo $_SESSION['usuario_nome']; ?></span>
                <a class="nav-link" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($mensagem): ?>
            <div class="alert alert-success">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-danger">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Nova Despesa</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="valor" name="valor" required>
                            </div>

                            <div class="mb-3">
                                <label for="data_despesa" class="form-label">Data</label>
                                <input type="date" class="form-control" id="data_despesa" name="data_despesa" required>
                            </div>

                            <button type="submit" name="cadastrar_despesa" class="btn btn-primary">Cadastrar Despesa</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Minhas Despesas</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($despesas)): ?>
                            <p class="text-muted">Nenhuma despesa cadastrada.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($despesas as $despesa): ?>
                                            <tr>
                                                <td><?php echo $despesa['titulo']; ?></td>
                                                <td><?php echo $despesa['descricao']; ?></td>
                                                <td>R$ <?php echo number_format($despesa['valor'], 2, ',', '.'); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($despesa['data_despesa'])); ?></td>
                                                <td>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="despesa_id" value="<?php echo $despesa['id']; ?>">
                                                        <button type="submit" name="excluir_despesa" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta despesa?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                    <a href="editar_despesa.php?id=<?php echo $despesa['id']; ?>" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>