<?php

$tokenBot = "8176524376:AAGB7CvDkBk1xAJfeWgB4NxcAFzuf0Lctg0";
$chat_id = "1431110316";

// ===== GITHUB =====
$github_token = "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";
$repo = "alexandrejpa1/esp-comandos";
$file = "comando.txt";

// =================
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) exit;

$chatId = $update["message"]["chat"]["id"];
$message = strtoupper(trim($update["message"]["text"]));

$cmd = "";

if ($message == "LIGAR") {
    $cmd = "LIGAR1";
}
elseif ($message == "DESLIGAR") {
    $cmd = "DESLIGAR1";
}
else {
    $resposta = "Use: LIGAR ou DESLIGAR";
}

// ===== ATUALIZAR GITHUB =====
if ($cmd != "") {

    // pegar SHA atual
    $url = "https://api.github.com/repos/$repo/contents/$file";

    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: PHP\r\nAuthorization: token $github_token\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $data = json_decode(file_get_contents($url, false, $context), true);

    $sha = $data["sha"];

    // novo conteúdo
    $conteudo = base64_encode($cmd);

    $payload = json_encode([
        "message" => "Atualizando comando",
        "content" => $conteudo,
        "sha" => $sha
    ]);

    $opts = [
        "http" => [
            "method" => "PUT",
            "header" => "User-Agent: PHP\r\nAuthorization: token $github_token\r\nContent-Type: application/json\r\n",
            "content" => $payload
        ]
    ];

    $context = stream_context_create($opts);
    file_get_contents($url, false, $context);

    $resposta = "✅ Comando enviado: $cmd";
}

// ===== RESPONDER TELEGRAM =====
file_get_contents("https://api.telegram.org/bot$tokenBot/sendMessage?chat_id=$chatId&text=" . urlencode($resposta));

echo "OK";
?>
