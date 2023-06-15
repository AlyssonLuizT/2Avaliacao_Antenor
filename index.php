<?php
//Alysson Luiz Tavares Da Rcoha-01359689
//Código feito em PHP. Para rodar o código é preciso as extensões do PHP (PHP Intelephense, PHP Debug, Open PHP) e um programa a parte  chamado "Xampp"
//É necessário colocar a pasta do código numa pasta dentro do Xampp chamada "htdocs"
//Depois disso é so clicar na tela com o botão direito do Mouse e escolher a opção "Open in browser"
function transformar_glc_para_fng($gramatica) {
    // Cria um novo símbolo inicial
    $nova_gramatica = ["S' -> " . trim(explode('->', $gramatica[0])[0])];

    // Remove regras vazias
    foreach ($gramatica as $regra) {
        $partes = explode('->', $regra);
        $nao_terminal = trim($partes[0]);
        $corpos = explode('|', $partes[1]);

        foreach ($corpos as $corpo) {
            if (trim($corpo) != 'λ') {
                $nova_gramatica[] = trim($regra);
            }
        }
    }

    // Remove regras unitárias
    $regras_unitarias = [];
    foreach ($nova_gramatica as $regra) {
        $partes = explode('->', $regra);
        if (count($partes) == 2 && ctype_upper(trim($partes[1]))) {
            $regras_unitarias[] = trim($regra);
        }
    }

    while (!empty($regras_unitarias)) {
        $regra_unitaria = array_shift($regras_unitarias);
        $partes = explode('->', $regra_unitaria);
        $nao_terminal = trim($partes[0]);
        $nao_terminal_unitario = trim($partes[1]);

        foreach ($nova_gramatica as $regra) {
            $partes = explode('->', $regra);
            if (count($partes) == 2 && trim($partes[0]) == $nao_terminal_unitario) {
                $nova_regra = $nao_terminal . " -> " . trim($partes[1]);
                if (!in_array($nova_regra, $nova_gramatica)) {
                    $regras_unitarias[] = $nova_regra;
                    $nova_gramatica[] = $nova_regra;
                }
            }
        }
    }

    // Elimina símbolos inúteis
    $simbolos_utilizados = [];
    $simbolos_utilizados[] = trim(explode('->', $nova_gramatica[0])[0]);

    foreach ($nova_gramatica as $regra) {
        $partes = explode('->', $regra);
        $nao_terminal = trim($partes[0]);
        $corpos = explode('|', $partes[1]);

        foreach ($corpos as $corpo) {
            foreach (explode(' ', trim($corpo)) as $simbolo) {
                if (ctype_upper($simbolo)) {
                    $simbolos_utilizados[] = $simbolo;
                }
            }
        }
    }

    $nova_gramatica = array_filter($nova_gramatica, function($regra) use ($simbolos_utilizados) {
        return in_array(trim(explode('->', $regra)[0]), $simbolos_utilizados);
    });

    $nova_gramatica = array_map(function($regra) use ($simbolos_utilizados) {
        return str_replace("S'", $simbolos_utilizados[0], $regra);
    }, $nova_gramatica);

    return $nova_gramatica;
}

// Exemplo de uso
$gramatica = [
    "S -> AB | BCS",
    "A -> aA | C",
    "B -> bbB | b",
    "C -> cC | λ"
];

$fng = transformar_glc_para_fng($gramatica);
foreach ($fng as $regra) {
    echo $regra . "\n";
}

?>