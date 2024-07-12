<?php

//Configurar injeção de dependência para o Slim
$container = new DI\Container();
$container->set('swooleServer', $http);