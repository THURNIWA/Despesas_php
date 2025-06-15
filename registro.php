<?php
session_start();
require_once 'config/database.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $erro = "Todos os campos são obrigatórios";
    } elseif ($senha != $confirmar_senha) {
        $erro = "As senhas não coincidem";
    } else {
        // Verificar se o email já existe
        $consulta = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($conexao, $consulta);

        if (mysqli_num_rows($resultado) > 0) {
            $erro = "Este email já está cadastrado";
        } else {
            // Cadastrar novo usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $consulta = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha_hash')";

            if (mysqli_query($conexao, $consulta)) {
                $_SESSION['mensagem'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                header("Location: login.php");
                exit();
            } else {
                $erro = "Erro ao cadastrar: " . mysqli_error($conexao);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Despesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Registro de Usuário</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($erro): ?>
                            <div class="alert alert-danger">
                                <?php echo $erro; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo isset($nome) ? $nome : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                                <a href="login.php" class="btn btn-link">Já tem uma conta? Faça login</a>
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