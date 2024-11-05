<?php
$host = 'localhost';
$db = 'tarefas_db';
$user = 'root';
$pass = '';

$mysqli = new mysqli($host, $user, $pass, $db);

// Verifica a conexão com o banco de dados
if ($mysqli->connect_error) {
    die(json_encode(["status" => "error", "message" => "Conexão falhou: " . $mysqli->connect_error]));
}

// Define o cabeçalho para JSON
header('Content-Type: application/json');

// Tratamento de requisições GET (obter tarefas)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $mysqli->query("SELECT * FROM tarefas");
    if ($result) {
        $tarefas = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($tarefas);
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao buscar tarefas"]);
    }
}

// Tratamento de requisições POST (adicionar nova tarefa)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    if (isset($dados['descricao']) && !empty($dados['descricao'])) {
        $descricao = $mysqli->real_escape_string($dados['descricao']);
        if ($mysqli->query("INSERT INTO tarefas (descricao) VALUES ('$descricao')")) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao inserir tarefa"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Descrição inválida"]);
    }
}

// Tratamento de requisições PUT (atualizar tarefa para concluída/não concluída)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put_vars);
    if (isset($put_vars['id'])) {
        $id = (int)$put_vars['id'];
        if ($mysqli->query("UPDATE tarefas SET concluida = NOT concluida WHERE id = $id")) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao atualizar tarefa"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID inválido"]);
    }
}

// Tratamento de requisições DELETE (excluir tarefa)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
        if ($mysqli->query("DELETE FROM tarefas WHERE id = $id")) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao excluir tarefa"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "ID inválido"]);
    }
}

$mysqli->close();
?>
