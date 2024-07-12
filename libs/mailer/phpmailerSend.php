<?php
namespace Lib\api;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class phpmailerSend
{
    public static function enviarEmail($dados)
    {
        $mailer = new PHPMailer(true);

        try {
            // ConfiguraÃ§Ãµes do servidor de e-mail
            $mailer->isSMTP();
            $mailer->Host = 'smtp.exemplo.com.br'; // EndereÃ§o do servidor SMTP
            $mailer->SMTPAuth = true;
            $mailer->Username = 'naoresponder@exemplo.com.br'; // UsuÃ¡rio do SMTP
            $mailer->Password = '*****'; // Senha do SMTP
            $mailer->SMTPSecure = 'ssl';
            $mailer->Port = 465; // Porta TCP para conexÃ£o
            $mailer->CharSet = 'UTF-8';
            //debug
            $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            $mailer->Debugoutput = 'html';
            $mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            // Remetente
            $mailer->setFrom('naoresponder@exemplo.com.br', 'Exemplo');

            // DestinatÃ¡rio
            $mailer->addAddress('contato@exemplo.com.br', 'Exemplo');

            //replay
            $mailer->addReplyTo($dados['email'], $dados['nome']);

            // Contedo do e-mail
            $mailer->isHTML(true);
            $mailer->Subject = $dados['title'];

            // Corpo do e-mail em HTML
            $bodyHTML = '
                <!DOCTYPE html>
                <html lang="pt-br">
                <head>
                    <meta charset="UTF-8">
                    <title>E-mail de Contato</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6em;
                            background-color: #f7f7f7;
                            margin: 0;
                            padding: 0;
                        }
                        h2 {
                            color: #2c3e50;
                        }
                        p {
                            color: #333;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #fff;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            border-radius: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>' . htmlspecialchars($dados['title']) . '</h2>
                        <p><strong>Nome:</strong> ' . htmlspecialchars($dados['nome']) . '</p>
                        <p><strong>Telefone:</strong> ' . htmlspecialchars($dados['contato']) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($dados['email']) . '</p>
                        <div>' . htmlspecialchars($dados['msg']) . '</div>
                    </div>
                </body>
                </html>';

            $mailer->Body = $bodyHTML;

            // Enviar o e-mail
            $mailer->send();
           return true;
        } catch (Exception $e) {
            var_dump("Erro ao enviar e-mail: {$e->getMessage()}");
            return false;
        }
    }
}

// // Exemplo de uso
// $dados = [
//     'title' => 'Assunto do E-mail',
//     'nome' => 'Nome do Remetente',
//     'contato' => '123456789',
//     'email' => 'remetente@exemplo.com',
//     'msg' => 'Esta Ã© a mensagem do e-mail.'
// ];
