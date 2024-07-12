# Documentação do Projeto
# obs: o projeto está em desenvolvimento e ainda não foi finalizado.

## Descrição Geral
Este projeto foi desenvolvido utilizando o Slim Framework 4 para rodar com Swoole no PHP. Ele incorpora diversos frameworks para facilitar o desenvolvimento, incluindo o Eloquent do Laravel para a criação de models.

## Estrutura de Pastas

### @docker
A pasta `docker` contém configurações e scripts necessários para a configuração e execução do ambiente Docker. Aqui estão alguns arquivos importantes:

- **config/swoole/entrypoint.sh**: Script de entrada para iniciar o contêiner Docker.
- **config/swoole/entrypointDev.sh**: Script de entrada para o ambiente de desenvolvimento.
- **config/swoole/restart.sh**: Script para reiniciar o servidor PHP.
- **config/swoole/requirements.txt**: Arquivo de dependências do Python.
- **config/nginx/nginx.conf**: Configuração do Nginx.
- **config/haproxy/haproxy.cfg**: Configuração do HAProxy.
- **config/mysql/dump/app_db.sql**: Dump do banco de dados MySQL.
- **config/postgres/dump/phpswoole_postgress_create.sql**: Script de criação do banco de dados PostgreSQL.

### @libs
A pasta `libs` contém bibliotecas e utilitários utilizados no projeto. Aqui estão alguns arquivos importantes:

- **slim/AppSlim.php**: Configuração e inicialização do aplicativo Slim.
- **slim/uploadImage.php**: Funções para upload de imagens.
- **slim/filter.php**: Funções de filtro.
- **swagger/swagger.json**: Arquivo de configuração do Swagger.
- **swagger/autogen.php**: Script para gerar automaticamente a documentação Swagger.
- **swagger/autogenModels.php**: Script para gerar automaticamente os modelos Swagger.
- **database/eloquent/config.php**: Configuração do Eloquent ORM.
- **migrations/migrate.php**: Script para gerenciar migrações do banco de dados.
- **migrations/migrate-install.php**: Script para instalar migrações.
- **migrations/migrate-reset.php**: Script para resetar migrações.
- **migrations/migrate-rollback.php**: Script para reverter migrações.
- **jwt/jwt.php**: Funções para geração e validação de tokens JWT.
- **mailer/phpmailerSend.php**: Funções para envio de e-mails utilizando PHPMailer.

### @bootstrap
A pasta `bootstrap` contém arquivos de inicialização e configuração do projeto. Aqui estão alguns arquivos importantes:

- **Container.php**: Configuração de injeção de dependência para o Slim.
- **SwooleMethod.php**: Configurações de cabeçalhos e métodos para o Swoole.

## Frameworks Utilizados
- **Slim Framework 4**: Utilizado para a criação de rotas e middleware.
- **Swoole**: Utilizado para melhorar a performance do servidor PHP.
- **Eloquent (Laravel)**: Utilizado para a criação e manipulação de models.
- **PHPMailer**: Utilizado para envio de e-mails.
- **JWT (Firebase)**: Utilizado para geração e validação de tokens JWT.

## Como Executar
1. **Configurar o Ambiente Docker**:
   - Utilize os scripts de entrada (`entrypoint.sh` e `entrypointDev.sh`) para configurar e iniciar o contêiner Docker.
   - Utilize o script `restart.sh` para reiniciar o servidor PHP quando necessário.

2. **Configurar o Banco de Dados**:
   - Utilize os scripts de dump (`app_db.sql` e `phpswoole_postgress_create.sql`) para configurar o banco de dados.

3. **Executar Migrações**:
   - Utilize os scripts de migração (`migrate.php`, `migrate-install.php`, `migrate-reset.php`, `migrate-rollback.php`) para gerenciar as migrações do banco de dados.

4. **Iniciar o Servidor**:
   - Utilize o script `server.php` para iniciar o servidor PHP com Swoole.

## Exemplos de Uso
### Upload de Imagens
- php: `libs/slim/uploadImage.php`
- startLine: 14
- endLine: 24

### Envio de E-mails
- php: `libs/mailer/phpmailerSend.php`
- startLine: 9
- endLine: 96


### Configuração de Middleware no Slim
- php: `libs/slim/AppSlim.php`
- startLine: 51
- endLine: 78


## Testes
Os testes unitários estão localizados na pasta `app/src/Tests/Unit/libs`. Aqui estão alguns arquivos de teste importantes:

- **SwooleAppSlimTest.php**: Testes para a aplicação Slim com Swoole.
- **AppSlimTest.php**: Testes para a aplicação Slim.
- **JwtTest.php**: Testes para a geração e validação de tokens JWT.

### Exemplo de Teste
- php: `app/src/Tests/Unit/libs/JwtTest.php`
- startLine: 27
- endLine: 85

### Ainda estou desenvolvendo esses conjustes de frameworks com swoole para mais performace.

## obs: falta fila asincrona (server para tudo), ainda estou desenvolvendo o projeto.