<?php
function validateInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}
function converterParaSaoPaulo(string $dataUTC): string {
    // Cria objeto DateTime no timezone UTC
    $date = DateTime::createFromFormat('d/m/Y H:i', $dataUTC, new DateTimeZone('UTC'));
    
    if (!$date) {
        throw new Exception("Data inválida: $dataUTC");
    }

    // Define o timezone de destino (São Paulo)
    $date->setTimezone(new DateTimeZone('America/Sao_Paulo'));

    // Retorna a data formatada no timezone de São Paulo
    return $date->format('d/m/Y H:i');
}
 

function extrairPlaca(string $string): string
{
    $string = strtoupper($string);

    // Modelo Mercosul: ABC1D23
    if (preg_match('/[A-Z]{3}[0-9][A-Z][0-9]{2}/', $string, $matches)) {
        return $matches[0];
    }

    // Modelo antigo: ABC1234
    if (preg_match('/[A-Z]{3}[0-9]{4}/', $string, $matches)) {
        return $matches[0];
    }

    return "desconhecida";
}






?>
