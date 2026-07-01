<?php
header('Content-Type: application/json; charset=utf-8');

// Bloqueia requisições que não sejam POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// -----------------------------------------------------------------------
// Configuração — altere os valores abaixo antes de publicar o site
// -----------------------------------------------------------------------
define('DESTINATARIO', 'contato@raiodosol.com.br'); // E-mail da clínica
define('REMETENTE',    'no-reply@raiodosol.com.br'); // Domínio do servidor
// -----------------------------------------------------------------------

/**
 * Remove tags HTML, espaços extras e caracteres especiais perigosos.
 */
function clean(string $value): string
{
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

// Coleta e sanitiza os dados do formulário
$name          = clean($_POST['name']          ?? '');
$email         = trim($_POST['email']          ?? '');
$phone         = clean($_POST['phone']         ?? '');
$especialidade = clean($_POST['especialidade'] ?? '');
$date          = clean($_POST['date']          ?? '');
$message       = clean($_POST['message']       ?? '');

// Validação dos campos obrigatórios
if ($name === '' || $email === '') {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha os campos obrigatórios.']);
    exit;
}

// Validação do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'E-mail inválido. Verifique e tente novamente.']);
    exit;
}

// Sanitiza o e-mail apenas após a validação
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

// Formata a data para exibição
$dataFormatada = $date !== ''
    ? date('d/m/Y', strtotime($date))
    : 'Não informada';

// Monta o corpo do e-mail
$body  = "Novo pedido de agendamento recebido pelo site Raio de Sol:\n\n";
$body .= "Nome:           {$name}\n";
$body .= "E-mail:         {$email}\n";
$body .= "Telefone:       " . ($phone !== '' ? $phone : 'Não informado') . "\n";
$body .= "Especialidade:  " . ($especialidade !== '' ? $especialidade : 'Não informada') . "\n";
$body .= "Data preferida: {$dataFormatada}\n\n";
$body .= "Mensagem:\n" . ($message !== '' ? $message : '—') . "\n";

// Cabeçalhos do e-mail
$subject = '=?UTF-8?B?' . base64_encode('Novo agendamento — Raio de Sol') . '?=';
$headers  = "From: " . REMETENTE . "\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Envio
if (mail(DESTINATARIO, $subject, $body, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Agendamento enviado com sucesso! Em breve entraremos em contato para confirmar seu horário.',
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Não foi possível enviar. Por favor, tente novamente ou entre em contato pelo WhatsApp.',
    ]);
}
