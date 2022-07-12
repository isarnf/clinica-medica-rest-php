<?php
 
    require_once("./configs/BancoDados.php");
    require_once("./configs/json/header.php");
    require_once("./configs/json/utils.php");
    require_once("./configs/json/verbs.php");
    require_once("./configs/Medico.php");

    // ============================ GET =================================

    if (isMetodo("GET")) {
        $medicos = Medico::listarMedicos();
        if ($medicos == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Não há médicos cadastrados!"
            ]);
            die;
        }else{
            header("HTTP/1.1 200 OK");
            echo json_encode($medicos);
            die;
        }
    }

    // ============================ POST ================================

    if (isMetodo("POST")) {
        if(!parametrosValidos($_POST, ["nome", "crm", "id_especialidade"])) {
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetros inválidos!"
            ]);
            die;
        }

        $nome = $_POST["nome"];
        $crm = $_POST["crm"];
        $id_especialidade = $_POST["id_especialidade"];
        
        if(emptyString($nome)){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'nome' vazio!"
            ]);
            die;
        }

        if (!filterIsInt($crm)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'crm' não é um número inteiro!"
            ]);
            die;
        }

        if($crm < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'crm' não pode ser um número negativo!"
            ]);
            die;
        }

        if (!filterIsInt($id_especialidade)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id_especialidade' não é um número inteiro!"
            ]);
            die;
        }

        if($id_especialidade < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id_especialidade' não pode ser um número negativo!"
            ]);
            die;
        }

        $res = Medico::adicionarMedico($nome, $crm, $id_especialidade);

        if($res){

            $medicos = Medico::listarMedicos();

            foreach($medicos as $medico) {
                if ($medico["nome"] == $nome and $medico["crm"] == $crm and $medico["id_especialidade"] == $id_especialidade){
                    $id = $medico["id"];
                }
            }
            header("HTTP/1.1 201 Created");
            echo json_encode([
                "status" => "Created",
                "msg" => "Médico de id = $id, nome = $nome, crm = $crm e id_especialidade = $id_especialidade adicionado com sucesso!"
            ]);
            die;
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "msg" => "Não foi possivel inserir médico!"
            ]);
            die;
        }
    }

    // ============================ PUT =================================

    if (isMetodo("PUT")) {
        if(!parametrosValidos($_PUT, ["id", "nome", "crm", "id_especialidade"])) {
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetros inválidos!"
            ]);
            die;
        }

        $id = $_PUT["id"];
        $nome = $_PUT["nome"];
        $crm = $_PUT["crm"];
        $id_especialidade = $_PUT["id_especialidade"];

        if (!filterIsInt($id)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não é número inteiro!"
            ]);
            die;
        }

        if($id < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não pode ser um número negativo!"
            ]);
            die;
        }

        if(emptyString($nome)){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'nome' vazio!"
            ]);
            die;
        }

        if (!filterIsInt($crm)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'crm' não é número inteiro!"
            ]);
            die;
        }

        if($crm < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'crm' não pode ser um número negativo!"
            ]);
            die;
        }

        if (!filterIsInt($id_especialidade)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id_especialidade' não é um número inteiro!"
            ]);
            die;
        }

        if($id_especialidade < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id_especialidade' não pode ser um número negativo!"
            ]);
            die;
        }

        $medico = Medico::buscarMedico($id);

        if ($medico == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Médico de id = $id não encontrado!"
            ]);
            die; 
        }

        $res = Medico::editarMedico($id, $nome, $crm, $id_especialidade);

        if ($res) {
            header("HTTP/1.1 200 OK");
            echo json_encode([
                "status" => "OK",
                "msg" => "Médico de id = $id editado com sucesso!"
            ]);
            die;
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "msg" => "Não foi possivel editar o médico com id = $id!"
            ]);
            die;
        } 
    }

    // ============================ DELETE ===============================

    if (isMetodo("DELETE")) {
        if (!parametrosValidos($_DELETE, ["id"])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não especificado!"
            ]);
            die;
        }

        $id = $_DELETE["id"];

        if (!filterIsInt($id)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não é um inteiro!"
            ]);
            die;
        }

        if($id < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não pode ser um número negativo!"
            ]);
            die;
        }
        
        $medico = Medico::buscarMedico($id);

        if ($medico == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "msg" => "Medico de id = $id não encontrado!"
            ]);
            die;
        } else {
            $res = Medico::deletarMedico($id);
            if ($res) {
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "status" => "OK",
                    "msg" => "Médico de id = $id deletado com sucesso!"
                ]);
                die;
            }else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Não foi possivel deletar o médico com id = $id!"
                ]);
                die;
            }
        }
    }



?>
