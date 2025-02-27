(Due to technical issues, the search service is temporarily unavailable.)

# Projeto CodeIgniter 4

Bem-vindo ao repositório do projeto desenvolvido em **CodeIgniter 4**! Este é um projeto de exemplo que demonstra como criar uma aplicação web robusta usando o framework CodeIgniter 4. Abaixo, você encontrará instruções detalhadas para clonar o repositório, configurar o ambiente e rodar a aplicação.

---

## 📋 Pré-requisitos

Antes de começar, certifique-se de que você possui os seguintes requisitos instalados em sua máquina:

- **PHP** (versão 8.0 ou superior)
- **Composer** (gerenciador de dependências para PHP)
- **MySQL** ou outro banco de dados suportado pelo CodeIgniter 4
- **Git** (para clonar o repositório)

---

## 🚀 Como clonar e rodar o projeto

Siga os passos abaixo para configurar e rodar o projeto em sua máquina:

### 1. Clone o repositório

Abra o terminal e execute o seguinte comando para clonar o repositório:

```bash
git clone https://github.com/LuizGabrielVieiraLeal/l5_teste.git
```

Navegue até a pasta do projeto:

```bash
cd seu-repositorio
```

---

### 2. Instale as dependências

O CodeIgniter 4 utiliza o Composer para gerenciar dependências. Para instalar todas as dependências necessárias, execute:

```bash
composer install
```

Isso instalará todas as bibliotecas e pacotes necessários para o funcionamento da aplicação.

---

### 3. Configure o ambiente

#### a) Crie o arquivo `.env`

O arquivo `.env` contém as variáveis de ambiente necessárias para rodar a aplicação.

#### b) Configure as informações do banco de dados

Abra o arquivo `.env` no seu editor de texto favorito e preencha as informações de conexão com o banco de dados. Procure pelas seguintes linhas e atualize-as com os dados do seu banco de dados:

```env
database.default.hostname = localhost
database.default.database = nome_do_banco
database.default.username = usuario_do_banco
database.default.password = senha_do_banco
database.default.DBDriver = MySQLi
```

**⚠️ Atenção:** Não se esqueça de preencher corretamente as informações de conexão com o banco de dados. Caso contrário, a aplicação não funcionará.

#### c) Gere uma nova chave de aplicação

O CodeIgniter 4 utiliza uma chave de aplicação para criptografia e segurança. Para gerar uma nova chave, execute o seguinte comando:

```env
JWT_SECRET_KEY = secret
```

---

### 4. Execute as migrations

Para criar as tabelas necessárias no banco de dados, execute as migrations:

```bash
php spark migrate
```

Isso criará todas as tabelas definidas nas migrations do projeto.

---

### 5. Inicie o servidor de desenvolvimento

Para rodar a aplicação localmente, utilize o servidor embutido do PHP. Execute o seguinte comando:

```bash
php spark serve
```

A aplicação estará disponível em:  
[http://localhost:8080](http://localhost:8080)

---

## 📝 Configurações Adicionais

### Banco de Dados

Certifique-se de que o banco de dados esteja configurado corretamente no arquivo `.env`. Se você estiver usando um banco de dados diferente do MySQL, atualize o `DBDriver` conforme necessário.

### Ambiente de Produção

Ao implantar a aplicação em um ambiente de produção, defina a variável `CI_ENVIRONMENT` no arquivo `.env` para `development`:

```env
CI_ENVIRONMENT = development
```

Isso garantirá que a aplicação esteja em modo de produção, com configurações de segurança otimizadas.

---

## 🙏 Agradecimentos

- Equipe do **CodeIgniter** pelo incrível framework.
- Comunidade de desenvolvedores PHP por todo o suporte e recursos disponíveis.

---

Feito com ❤️ por [Luiz Gabriel Vieira Leal](https://github.com/seu-usuario).  
Se tiver alguma dúvida, sinta-se à vontade para abrir uma issue ou entrar em contato!