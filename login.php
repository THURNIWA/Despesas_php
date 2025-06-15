<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = "E-mail e senha são obrigatórios";
    } else {
        $consulta = "SELECT id, nome, senha FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($conexao, $consulta);

        if (mysqli_num_rows($resultado) == 1) {
            $usuario = mysqli_fetch_assoc($resultado);
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("Location: dashboard.php");
                exit();
            } else {
                $erro = "Senha incorreta";
            }
        } else {
            $erro = "E-mail não encontrado";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Despesas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['mensagem'])): ?>
                            <div class="alert alert-success">
                                <?php
                                echo $_SESSION['mensagem'];
                                unset($_SESSION['mensagem']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($erro): ?>
                            <div class="alert alert-danger">
                                <?php echo $erro; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Entrar</button>
                                <a href="registro.php" class="btn btn-link">Não tem uma conta? Registre-se</a>
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