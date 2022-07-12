<?php
  
    require_once("./configs/BancoDados.php");
    require_once("./configs/json/header.php");
    require_once("./configs/json/utils.php");
    require_once("./configs/json/verbs.php");
    require_once("./configs/Consulta.php");
    require_once("./configs/Medico.php");
    require_once("./configs/Paciente.php");


    // ============================ GET =================================

    if (isMetodo("GET")) {     
        $consultas = Consulta::listarConsultas();   
        if ($consultas == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "msg" => "Não há consultadas cadastradas!"
            ]);
            die;
        } else {
            header("HTTP/1.1 200 OK");
            echo json_encode($consultas);
            die;
        }
    }
    
    // ============================ POST ================================

    if (isMetodo("POST")) {
        if(!parametrosValidos($_POST, ["data_consulta", "id_medico", "id_paciente"])){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetros inválidos!"
            ]);
            die;
        }

        $dataConsulta = $_POST["data_consulta"];
        $idMedico = $_POST["id_medico"];
        $idPaciente = $_POST["id_paciente"];

        if (!dataHoraValida($data, $horario, $formato)) {      
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'data da Consulta' não é válido",
            ]);
            die;
        }      

        if (!filterIsInt($id_medico)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idMedico' não é um inteiro!"
            ]);
            die;
        }

        if($id_medico < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idMedico' não pode ser um número negativo!"
            ]);
            die;
        }

        if (!filterIsInt($id_paciente)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idPaciente' não é um inteiro!"
            ]);
            die;
        }

        if($id_paciente < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idPaciente' não pode ser um número negativo!"
            ]);
            die;
        }

        $paciente_procurado = Paciente::listarPaciente($id_paciente); //conferir nome função listarPaciente ou buscarPaciente

        if($paciente_procurado == null){
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Paciente com id = $id_paciente não encontrado no sistema!"
            ]);
            die;
        }

        $medico_procurado = Medico::buscarMedico($id_medico);

        if($medico_procurado == null){
                header("HTTP/1.1 404 Not Found");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Médico com id = $id_medico não encontrado no sistema!"
            ]);
            die;
        }

        $res = Consulta::adicionarConsulta($data_consulta, $id_medico, $id_paciente);

        if ($res) {

            $consultas = Consulta::listarConsultas();

            foreach($consultas as $consulta) {
                if ($consulta["data_consulta"] == $data_consulta and $consulta["id_medico"] == $id_medico and $consulta["id_paciente"] == $id_paciente){
                    $id = $consulta["id"];
                }    
            }
            header("HTTP/1.1 201 Created");
            echo json_encode([
                "status" => "Created",
                "msg" => "Consulta de id = $id, data = $data_consulta, idMedico = $id_medico e idPaciente = $id_paciente adicionada com sucesso!"
            ]);
            die;
        } else { 
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "msg" => "Não foi possivel inserir a consulta!"
            ]);
            die; 
        }
    }


    // ============================ PUT =================================

    if (isMetodo("PUT")) {
        if (!parametrosValidos($_PUT, ["id", "data_consulta", "id_medico", "id_paciente"])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetros inválidos!"
            ]);
            die;
        }
            
        $id = $_PUT["id"];
        $dataConsulta = $_PUT["data_consulta"];
        $idMedico = $_PUT["id_medico"];
        $idPaciente = $_PUT["id_paciente"];

        if (!filterIsInt($id)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'id' não é inteiro!"
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

        if (Consulta::listarConsulta($id) == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Consulta de id = $id não existe!"
            ]);
            die;
        }

        if (!dataHoraValida($data, 'Y-m-d H:i')) {      
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'data da Consulta' não é válido",
            ]);
            die;
        } 

        if (!filterIsInt($id_medico)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idMedico' não é um inteiro!"
            ]);
            die;
        }

        if($id_medico < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idMedico' não pode ser um número negativo!"
            ]);
            die;
        }

        if (!filterIsInt($id_paciente)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idPaciente' não é um inteiro!"
            ]);
            die;
        }

        if($id_paciente < 0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => "error",
                "msg" => "Parâmetro 'idPaciente' não pode ser um número negativo!"
            ]);
            die;
        }
            
        if (Medico::buscarMedico($id_medico) == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Médico de id = $id_medico não encontrado!"
            ]);
            die; 
        }

        if (Paciente::listarPaciente($id_paciente) == null) { // confirmar se está listarPaciente ou buscarPaciente
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Paciente de id = $id_paciente não encontrado!"
            ]);
            die; 
        }
    
        $res = Consulta::atualizarConsulta($id, $data_consulta, $id_medico, $id_paciente);
        if ($res) {
            header("HTTP/1.1 200 OK");
            echo json_encode([
                "status" => "OK",
                "msg" => "Consulta de id = $id editada com sucesso!"
            ]);
            die;
        } 
        else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode([
                "status" => "error",
                "msg" => "Não foi possivel editar a consulta com id = $id!"
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

        $consulta = Consulta::listarConsulta($id);

        if ($consulta == null) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode([
                "status" => "error",
                "msg" => "Consulta de id = $id não encontrada!"
            ]);
            die;
        } else {
            $res = Consulta::deletarConsulta($id);
            if ($res) {
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "status" => "OK",
                    "msg" => "Consulta de id = $id deletada com sucesso!"
                ]);
                die;
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode([
                    "status" => "error",
                    "msg" => "Não foi possivel deletar a consulta com id = $id!"
                ]);
                die;
            }
        }
    }   
?>
    
