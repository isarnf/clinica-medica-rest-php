<?php


function parametrosValidos($metodo, $lista)
{
    $obtidos = array_keys($metodo);
    $nao_encontrados = array_diff($lista, $obtidos);
    if (empty($nao_encontrados)) {
        foreach ($lista as $p) {
            if (empty(trim($metodo[$p])) and trim($metodo[$p]) != "0") {
                return false;
            }
        }
        return true;
    }
    return false;
}


function isMetodo($metodo)
{
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], $metodo)) {
        return true;
    }
    return false;
}


function filterIsInt($v) {
    return filter_var($v, FILTER_VALIDATE_INT);
}

function filterIsEmail($v) {
    return filter_var($v, FILTER_VALIDATE_EMAIL);
}

function emptyString($str) {
    if(strlen(trim($str)) == 0) {
        return true;
    } 
    return false;
}

function adminLoginIsUnique($login, $idAdmin = null) {
    require_once("./classes/Administrador.php");
    try {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("SELECT id, COUNT(*) AS qtde from administradores WHERE login = ?");
        $stmt->execute([$login]);

        $qtde = $stmt->fetchAll()[0]["qtde"];
        $id = $stmt->fetchAll()[0]["id"];
        if ($qtde == 0 || qtde > 0 and isset($idAdmin) and $id == $idAdmin)
            return true;;
        return false;
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode([
            "msg" => "Houve um erro na base de dados: " . $e->getMessage()
        ]);
        exit;
    }
}

function pacienteLoginIsUnique($login, $idPaciente = null) {
    require_once("./classes/Paciente.php");
    try {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("SELECT id, COUNT(*) AS qtde from pacientes WHERE login = ?");
        $stmt->execute([$login]);

        $qtde = $stmt->fetchAll()[0]["qtde"];
        $id = $stmt->fetchAll()[0]["id"];
        if ($qtde == 0 || qtde > 0 and isset($idPaciente) and $id == $idPaciente)
            return true;;
        return false;
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode([
            "msg" => "Houve um erro na base de dados: " . $e->getMessage()
        ]);
        exit;
    }
}

function dataEhValida($data, $formato) {

    try {
        $dateTime = DateTime::createFromFormat($formato, $data);
        $dia = $dateTime->format("d");
        $mes = $dateTime->format("m");
        $ano = $dateTime->format("Y");

        if (!checkdate($mes, $dia, $ano))
            return false;

        return true;
    } catch (Exception $e) {
        return false;
    }
}
