## como usar task para executar uma função

```php
// Criação de um canal específico para esta tarefa
$chan = new \Swoole\Coroutine\Channel(1);

// // Envie a tarefa e obtenha o task_id
$task_id = $swooleServer->task(
    [
        'namespace' => 'App\model\entity',
        'class' => 'peaples',
        'method' => 'create',
        'data' => $data, // Passe os dados diretamente sem decodificar
    ]
);

// Associe o canal ao task_id em uma estrutura compartilhada (por exemplo, um array estático)
TaskChannelManager::setChannel($task_id, $chan);

// Recebe o resultado da tarefa do canal
 $result2 = $chan->pop(5);  // Timeout de 5 segundos para evitar deadlock
 if ($result2 === false) {
     \var_dump("task timeout");
     $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Task timeout']));
     return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
}

/// Processa os dados após a conclusão da tarefa (verificar se esta vindo em json os dados)
$data = json_decode($result2, true);
```