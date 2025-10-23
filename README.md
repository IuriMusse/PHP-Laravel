# Desafio Técnico: CRUD de Usuários e Endereços (Laravel + Vue.js)

Este projeto implementa um sistema CRUD completo para gestão de usuários e seus respectivos endereços, seguindo a arquitetura de backend e frontend desacoplados. O projeto atende a todas as especificações obrigatórias e incorpora diversas melhorias.

## 1. Visão Geral e Requisitos

O projeto atende a todos os requisitos definidos no Desafio Técnico, com foco em:

1.  **CRUD Completo:** Criação, leitura (lista e detalhes), edição e exclusão de usuários.
2.  **Filtros de Pesquisa:** Nome, CPF e Período de Cadastro.
3.  **Regras de Relacionamento:**
    - Um usuário pertence a um perfil (1:N).
    - Um usuário pode ter múltiplos endereços (N:N).

---

## 2. Tecnologias Utilizadas

O projeto é dividido em duas aplicações que se comunicam via API REST:

| Camada               | Tecnologia            | Versão            | Detalhes                                                                  |
| -------------------- | --------------------- | ----------------- | ------------------------------------------------------------------------- |
| **Backend (Core)**   | **PHP**               | `8.2.12`          | Linguagem base do backend.                                                |
| **Backend (API)**    | **Laravel Framework** | `12.34.0`         | Framework PHP que fornece a API RESTful e a arquitetura MVC.              |
| **Frontend (Core)**  | **Vue.js**            | `3.5.22`          | Biblioteca JavaScript para a construção da SPA (Single Page Application). |
| **Frontend (Rotas)** | **Vue Router**        | `4.6.3`           | Gerenciamento de rotas da aplicação frontend.                             |
| **Banco de Dados**   | **MySQL/SQLite**      | `10.4.32-MariaDB` | Utilizado com Eloquent ORM.                                               |

---

## 3. Implementações

### 3.1. Escalabilidade, Validação de Dados e Performance (Paginação e Resources)

- **Validação Matemática de CPF (`RegraCpf.php`):** Uma Custom Rule foi criada para garantir a validade matemática de todo CPF antes de ser persistido no banco de dados.
- **Paginação (`UsuarioController@index`):** O endpoint de listagem retorna dados paginados (`->paginate(5)`), limitando a 5 usuários por página, o que garante a performance e a escalabilidade da API.
- **API Resources (`UsuarioResource.php`):** Toda a saída de dados do usuário é formatada por um API Resource, assegurando que o formato JSON seja consistente e que campos como CPF e data já cheguem formatados ao frontend.

### 3.2. Arquitetura e Robustez (Form Requests)

- **Validação com Form Requests:** As regras de validação para criação e atualização de usuário foram movidas do `UsuarioController.php` para classes dedicadas (`NovoUsuarioRequest` e `AtualizarUsuarioRequest`). Isso limpa o _controller_, seguindo o princípio da Separação de Responsabilidades (SRP).

### 3.3. Integridade de Dados (Limpeza de Órfãos)

- **Limpeza de Endereços Órfãos:** O `UsuarioController` implementa uma lógica de limpeza que, após atualizar ou excluir um usuário, verifica e **remove** da tabela `addresses` qualquer registro que não esteja mais vinculado a **nenhum outro** usuário, impedindo o acúmulo de lixo no banco de dados.

### 3.4. Sincronização

- **Paginação e Filtros:** O endpoint `/usuarios` suporta filtros dinâmicos e paginação, controlando a quantidade de itens por página (`por_pagina`) para escalabilidade.

---

## 4. Funcionalidades e UX

### 4.1. Gerenciamento de Endereços (Frontend)

- **Visibilidade Condicional:** O formulário detalhado para adicionar/editar um endereço (`EditaUsuario.vue`) é exibido apenas quando a ação é iniciada.
- **Controle de Fluxo:** O botão principal **Salvar** na tela de cadastro (`FormUsuario.vue`) só fica ativo se **pelo menos um endereço** já tiver sido adicionado à lista, forçando o fluxo correto de dados.
- **Controle de Estado:** O botão principal **Salvar Alterações** na edição (`EditaUsuario.vue`) fica desabilitado se houver uma operação de endereço pendente.
- **Cadastro Rápido de Perfil:** Adicionado um botão de `+` ao lado do campo Perfil no `FormUsuario.vue` para cadastrar um novo perfil dinamicamente, sem sair da tela de cadastro.

### 4.2. UI/UX e Validação Frontend

- **UX/UI:** O layout dos botões de ação na grid (`LisyaUsuarip.vue` e `FormEndereco.vue`) foi padronizado em cores e inclui **Tooltips** para melhor acessibilidade.
- **Máscaras e CEP:** Máscara de CPF e Máscara de CEP (`99999-999`) foram implementadas, com validação que exige exatamente 8 dígitos preenchidos antes de permitir a adição/envio.
- **Consistência:** O carregamento de dados foi corrigido para se adaptar ao formato aninhado do API Resource (`.data.data`), garantindo que `EditaUsuario.vue` e `DetalheUsuario.vue` carreguem corretamente.

---

## 5. Instruções de Execução

Para rodar este projeto em ambiente de desenvolvimento, siga os passos na ordem.

### 5.1. Backend (Laravel) - Configuração do Banco e Dados Iniciais

#### 1. Criação do Schema MySQL

Execute o comando SQL abaixo no seu cliente MySQL (ex: HeidiSQL, DBeaver, MySQL Workbench) ou linha de comando:

`sql`
CREATE DATABASE IF NOT EXISTS `desafio_laravel` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

#### 2. Configuração do `.env`

Certifique-se de que o seu arquivo `.env` tenha as configurações corretas (`DB_DATABASE=desafio_laravel`).

#### 3. Instalar, Migrar e Povoar

O Laravel irá criar as tabelas e inserir os dados iniciais do projeto (Perfís, Usuário e Endereço) através do `InitialDataSeeder`, sendo chamado pelo IniciarDatabase.

1.  Instale as dependências do Laravel: (Caso já tenha instalado, seguir para o passo 2)
    ```bash
    composer install
    ```
2.  Cria as tabelas e insere os dados iniciais:
    ```bash
    php artisan bd:iniciar
    ```
3.  Automaticamente irá iniciaalizar o servidor API pelo comando:

    ```bash
    php artisan serve

    ```

### 5.2. Frontend (Vue.js)

1.  Navegue para o diretório do frontend.
2.  Instale as dependências:
    ```bash
    npm install
    ```
3.  Verifique se o `VITE_API_BASE_URL` no `.env` aponta para a API do Laravel.
4.  Inicie o servidor de desenvolvimento:
    ```bash
    npm run dev
    ```
