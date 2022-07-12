<?php
   
    require_once("./configs/BancoDados.php");
    require_once("./configs/json/header.php");
    require_once("./configs/json/utils.php");
    require_once("./configs/json/verbs.php");
    require_once("./configs/Especialidade.php");
    

    // ============================ GET =================================

    if (isMetodo("GET")) {
        $especialidades = Especialidade::listarEspecialidades();
        if ($especialidades == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Não há especialidades cadastradas!"
            ]);
            die;
        }else{
            header("HTTP/1.1 200 OK");
            echo json_encode($especialidades);
            die;
        }
    }

    // ============================ POST ===============================

    if (isMetodo("POST")) {
        if(!parametrosValidos($_POST, ["nome"])) {
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro inválido!"
            ]);
            die;
        }

        $nome = $_POST["nome"];

        if(emptyString($nome)){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'nome' vazio!"
            ]);
            die;
        }

        $especialidade = Especialidade::buscarEspecialidadePorNome($nome);

        if ($especialidade == null) {

            $res = Especialidade::adicionarEspecialidade($nome);

            if($res){

                $especialidades = Especialidade::listarEspecialidades();

                foreach($especialidades as $esp) {
                    if ($esp["nome"] == $nome){
                        $id = $esp["id"];
                    }
                }

                header("HTTP/1.1 201 Created");
                echo json_encode([
                    "status" => "Created",
                    "msg" => "Especialidade de id = $id, nome = $nome adicionada com sucesso!"
                ]);
                die;

            }else{
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Não foi possivel inserir especialidade!"
                ]);
                die;
            }
        }else{
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Especialidade de nome = '$nome' já existe!"
            ]);
            die;
        }
    }

    // ============================ PUT =================================

    if (isMetodo("PUT")) {
        if(!parametrosValidos($_PUT, ["id", "nome"])) {
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetros inválidos!"
            ]);
            die;
        }

        $id = $_PUT["id"];
        $nome = $_PUT["nome"];

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

        $especialidade = Especialidade::buscarEspecialidadePorId($id);

        if ($especialidade == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Especialidade de id = $id não encontrada!"
            ]);
            die; 
        }

        $res = Especialidade::editarEspecialidade($id, $nome);

        if ($res) {
            header("HTTP/1.1 200 OK");
            echo json_encode([
                "status" => "OK",
                "msg" => "Especialidade de id = $id editada com sucesso!"
            ]);
            die;
        }else{
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "msg" => "Não foi possivel editar a especialidade com id = $id!"
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
        
        $especialidade = Especialidade::buscarEspecialidadePorId($id);

        if ($medico == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "msg" => "Especialidade de id = $id não encontrada!"
            ]);
            die;
        } else {
            $res = Especialidade::deletarEspecialidade($id);
            if ($res) {
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "status" => "OK",
                    "msg" => "Especialidade de id = $id deletada com sucesso!"
                ]);
                die;
            }else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Não foi possivel deletar a especialidade com id = $id!"
                ]);
                die;
            }
        }
    }


?>
