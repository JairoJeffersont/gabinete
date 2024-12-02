
# Gabinete Digital

## Clonar o Repositório Git

Para começar, clone o repositório Git executando o seguinte comando:

```bash
git clone https://github.com/JairoJeffersont/gabinete
```

Após o clone, coloque todos os arquivos na pasta da sua hospedagem em `meu_dominio.com.br/gabinete`.  
**Atenção**: os arquivos devem estar dentro de uma pasta chamada **gabinete**.

---

## Configurar as Variáveis de Ambiente

Antes de executar a aplicação, configure as variáveis de ambiente editando o arquivo `/src/Configs/config.php` com as seguintes informações:

```php
'database' => [
    'host' => 'localhost',          // Endereço do servidor do banco de dados
    'name' => 'nome_do_banco',      // Nome do banco de dados
    'user' => 'usuario_do_banco',   // Usuário do banco de dados
    'password' => 'senha_do_banco', // Senha do banco de dados
],

'master_user' => [
    'master_name' => 'Administrador', // Nome do usuário principal (recomenda-se personalizar para maior segurança)
    'master_email' => 'admin@admin.com', // Email do administrador (troque por um email único e seguro)
    'master_pass' => 'senha_adm', // Senha do administrador (utilize uma senha forte e exclusiva)
],

'deputado' => [
    'id' => '0000000',                  // ID do deputado (encontrado em https://www.camara.leg.br/deputados/quem-sao ou na API: https://dadosabertos.camara.leg.br/api/v2/deputados?ordem=ASC&ordenarPor=nome)
    'nome' => 'Nome do Deputado',       // Nome parlamentar do deputado
    'estado' => 'UF',                   // Estado (UF) do deputado
    'ano_primeiro_mandato' => 0000,     // Ano do primeiro mandato do deputado
],
```

---

## Sincronizar as Tabelas do Banco de Dados

Importe o script SQL fornecido em `/mysql/db.sql` no banco de dados configurado.

---

## Primeiro Acesso

Acesse o sistema pelo navegador no endereço:

```
meu_dominio.com.br/gabinete
```

Faça login utilizando as credenciais administrativas configuradas no passo anterior. Após o login, crie uma nova conta de administrador, caso necessário.

---

## Cadastro de Novos Usuários

Para permitir que novos usuários criem suas contas, compartilhe o link de cadastro:

```
meu_dominio.com.br/gabinete/?secao=cadastro
```

Peça para que os interessados preencham todos os campos. **Atenção**: as novas contas criadas ficarão desativadas e precisarão ser ativadas por um administrador.

---

Se precisar de mais assistência, é só perguntar! 😊
