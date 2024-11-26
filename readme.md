# Gabinete Digital

## Clonar o Repositório Git

Para começar, clone este repositório Git executando o seguinte comando:

```
git clone https://github.com/JairoJeffersont/gabinete
```
Coloque todos os arquivo na pasta da sua hospedagem. `meu_dominio.com.br/pasta_do_aplicativo`


## Configurar as Variáveis de Ambiente

Antes de executar a aplicação, é necessário configurar as variáveis de configuração. Modifique o arquivo `/src/Configs/config.php` com as seguintes variáveis:

```
    'database' => [
        'host' => 'localhost',
        'name' => 'nome_do_banco',
        'user' => 'usuario_do_banco',
        'password' => 'senha_do_banco',
    ],

    'master_user' => [
        'master_name' => 'Administrador', //nome do usuário administrativo
        'master_email' => 'admin@admin.com', //email do usuário administrativo
        'master_pass' => 'senha_adm', //senha
    ],

    'deputado' => [
        'id' => '0000000', //Esse id pode ser encontrado em https://www.camara.leg.br/deputados/quem-sao ou https://dadosabertos.camara.leg.br/api/v2/deputados?ordem=ASC&ordenarPor=nome
        'nome' => 'Nome do Deputado, //o nome parlamentar do deputado
        'estado' => 'UF', //estado do deputado
        'ano_primeiro_mandato' => 0000 //ano do primeiro mandato do deputado
    ],

```
## Sincronizar as tabelas do banco
Importe o sript sql no seu banco de dados. mysql/db.sql


## Primero acesso

Acesse `meu_dominio.com.br/pasta_do_aplicativo` e faça login com o usuário administrativo e crie sua nova conta.

## Novos usuários

Para permitir que outros usuário criem suas contas, acesse `meu_dominio.com.br/pasta_do_aplicativo/?secao=cadastro` e peça para que eles preencham os campos. Cada novo usuário estará desativado necessitando que um usuário administrativo ative sua conta.