# Desafio Laravel + Next

---

## Desafio e Regras

### Avaliação das skills
- Capacidade de codificar uma aplicação Laravel
- Criar tabelas no mysql
- Transação na importação
- Validadores de entrada de dados
- Validar conhecimentos de frontend com nextjs
- SQL com JOIN

### Objetivos
- Importar o csv enviado usando um comando do Laravel (php artisan import music.csv), adicionar validação, transação e importar em lote.
- Usar o laravel breeze com nextjs para logar no sistema
- Criar o endpoint /api/music/1/user/2 (POST) isso associa o usuário de código 2 a música de código 1
  Cada usuário pode ser associado a várias músicas.
- Criar uma tabela em react para mostrar a lista de músicas de cada usuário (usar SQL puro, sem usar Eloquent do Laravel para isso). (Mostrar todos usuários que e suas músicas)

---

## Como rodar

1. **Clonar** este repositório:
   ```bash
   git clone https://github.com/luizolivei/desafio-docker-laravel-next
   cd desafio-docker-laravel-next
   ```

2. **Construir** as imagens:
   ```bash
   docker-compose build
   ```

3. **Subir** os containers em segundo plano:
   ```bash
   docker-compose up -d
   ```

4. **Verificar** se os contêineres estão rodando:
   ```bash
   docker-compose ps
   ```

---

## Acessando a aplicação

- **Next.js**:  
  Acesse **http://localhost:3000**

- **Laravel**:  
  POST /api/music/1/user/2 ... ... ...

---

## Conectando ao banco MySQL

- **Banco**: `jukebox`
- **Usuário**: `laravel`
- **Senha**: `secret`
- **Root Password**: `root`

```
jdbc:mysql://localhost:3306/jukebox?user=laravel&password=secret
```