
# Explicação do Código: Busca e Processamento de Proposições da Câmara dos Deputados

Este código é responsável por buscar proposições e seus autores da Câmara dos Deputados, além de permitir gerar arquivos SQL para inserção dessas proposições em um banco de dados. Abaixo está uma explicação detalhada sobre cada parte do código.

---

## Importação de Bibliotecas

```python
from proposicoes.buscarProposicoes import inserirProposicoes, inserirAutoresProposicoes, criarSqlProposicoes, criarSqlAutores
import os
import shutil
from datetime import datetime
```

O código começa importando funções necessárias para o processo de inserção e criação de SQL das proposições e autores. Também importa `os`, `shutil` e `datetime` para interagir com o sistema de arquivos e manipular datas.

---

## Função Principal

A função principal começa limpando a tela do terminal e exibindo uma mensagem de inicialização:

```python
os.system('clear')
print("Iniciando busca de proposições e seus autores da Câmara dos Deputados.
")
```

O script então entra em um loop de opções para o usuário.

---

## Menu de Opções

O usuário pode escolher entre várias opções. A seguir, estão as opções apresentadas no menu:

### Opção 1: Inserir Proposições de um Ano Específico

```python
if opcao == '1':
    os.system('clear')
    ano = input("
Digite o ano desejado: ")
    print(f'
Buscando as proposições do ano {ano}. Isso pode levar alguns minutos...
')
    inserirProposicoes(ano)
    inserirAutoresProposicoes(ano)
```

O código solicita que o usuário forneça um ano específico e, em seguida, chama as funções `inserirProposicoes` e `inserirAutoresProposicoes` para buscar as proposições e autores desse ano.

### Opção 2: Inserir Todas as Proposições

```python
elif opcao == '2':
    resposta = input('
Tem certeza que deseja continuar? Esse procedimento pode levar vários minutos. (s/n): ').strip().lower()
    if resposta == 's':
        print('
Buscando todas as proposições.
')
        ano_atual = datetime.now().year
        for ano in range(1950, ano_atual + 1):
            print(f"
Processando proposições do ano {ano}...")
            try:
                inserirProposicoes(ano)
                inserirAutoresProposicoes(ano)
                print(f"Concluído o processamento do ano {ano}.")
            except Exception as e:
                print(f"Erro ao processar o ano {ano}: {e}")
                break  
    else:
        print("
Processo cancelado pelo usuário.")
```

Se o usuário escolher a opção 2, ele confirma a ação e o script começa a buscar proposições de todos os anos, de 1950 até o ano atual. Caso ocorra algum erro, o processo é interrompido.

### Opção 3: Criar SQL para um Ano Específico

```python
elif opcao == '3':
    ano = input("
Digite o ano desejado: ")
    print(f'Criando SQL com as proposições do ano {ano}. Isso pode levar alguns minutos...
')
    criarSqlProposicoes(ano)
    criarSqlAutores(ano)
```

Essa opção permite ao usuário gerar arquivos SQL para um ano específico.

### Opção 4: Criar SQL com Todas as Proposições

```python
elif opcao == '4':
    resposta = input('
Tem certeza que deseja continuar? Esse procedimento pode levar vários minutos. (s/n): ').strip().lower()
    pasta_sql = 'sql'
    if resposta == 's':
        print('
Buscando todas as proposições.
')
        if os.path.exists(pasta_sql) and os.path.isdir(pasta_sql):
            for arquivo in os.listdir(pasta_sql):
                caminho_arquivo = os.path.join(pasta_sql, arquivo)
                try:
                    if os.path.isfile(caminho_arquivo):
                        os.remove(caminho_arquivo)  # Apaga o arquivo
                except Exception as e:
                    print(f'Erro ao tentar apagar {arquivo}: {e}')
        ano_atual = datetime.now().year
        for ano in range(1950, ano_atual + 1):
            print(f"
Processando proposições do ano {ano}...")
            try:
                criarSqlProposicoes(ano)
                criarSqlAutores(ano)
                print(f"Concluído o processamento do ano {ano}.")
            except Exception as e:
                print(f"Erro ao processar o ano {ano}: {e}")
                break  
    else:
        print("
Processo cancelado pelo usuário.")
```

Caso o usuário escolha a opção 4, o código verifica se a pasta 'sql' existe e, em caso afirmativo, remove os arquivos dentro dela. Em seguida, ele gera os arquivos SQL para todas as proposições de 1950 até o ano atual.

### Opção 5: Sair

```python
elif opcao == '5':
    print("Saindo...

")
    break
```

Essa opção encerra o loop e sai do programa.

### Opção Inválida

Se o usuário digitar uma opção inválida, o código exibe uma mensagem de erro:

```python
else:
    print("Opção inválida. Tente novamente.")
```

---

## Conclusão

Este código automatiza o processo de busca, inserção e geração de SQL para proposições e seus autores na Câmara dos Deputados. Ele oferece ao usuário a flexibilidade de buscar proposições de um ano específico ou de todos os anos, além de gerar arquivos SQL para facilitar a inserção de dados em bancos de dados.

