# Desafio Backend

## Instalação

* Iniciar a aplicação `docker-compose up --build`.
* Criar o banco de dados `docker-compose exec php php artisan migrate --seed`.
* Iniciar o __Laravel Horizon__ `docker-compose exec -d php php artisan horizon`.
* _Opcional_:
  * `docker-compose exec php composer install` para instalar as dependências do Laravel.
  * `docker-compose exec php cp .env.example .env` para criar o arquivo `.env` (_obrigatório_).
  * `docker-compose exec php artisan key:generate` para gerar o `APP_KEY` (_obrigatório_).

## Documentação

O projecto foi construido utilizando o framework [Laravel](http://www.laravel.com). Abaixo os _endpoints_ e _exemplos de respostas_:

## Lista todos os usuários

```http
GET /api/users
```

__Exemplo__:

```bash
curl http://localhost:8000/api/users
```

```json
[
    {
        "id": 2,
        "name": "Sr. Fábio Azevedo",
        "email": "anita23@example.com",
        "document": "060.372.981-90",
        "logist": false,
        "balance": 7603
    },
    {
        "id": 4,
        "name": "Sr. Estêvão George Barreto",
        "email": "jonas76@example.org",
        "document": "518.949.137-44",
        "logist": false,
        "balance": 1599.8
    },
    {
        "id": 5,
        "name": "Srta. Norma Chaves Jr.",
        "email": "gael.alcantara@example.com",
        "document": "269.377.702-00",
        "logist": false,
        "balance": 1968.6
    },
]
```

## Exibe informações de determinado usuário

```http
GET /api/users/:id
```

__Exemplo__:

```bash
curl http://localhost:8000/api/users/1
```

```json
{
    "id": 1,
    "name": "Melissa Cordeiro Sobrinho",
    "email": "carolina85@example.org",
    "document": "123.858.160-91",
    "logist": true,
    "balance": 7002.2
}
```

## Cria um novo usuário

```http
POST /api/users
```

__Exemplo__:

```bash
curl -X POST http://localhost:8000/api/users -H "Content-Type: application/json" -d '{"name":"Diogo Siva","email":"dsilva98@hotmail.com","document":"539.064.320-84","password":"secret"}'
```

```json
{
    "status":"created",
    "data":{
        "id":7,
        "name":"Diogo Siva",
        "email":"dsilva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": false,
        "balance": 0
    }
}
```

## Atualiza os dados de determinado usuário

```http
PUT /api/users/:id
```

__Exemplo__:

```bash
curl -X PUT http://localhost:8000/api/users/7 -H "Content-Type: application/json" -d '{"name":"Diogo Silva"}'
```

```json
{
    "status":"updated",
    "data":{
        "id":7,
        "name":"Diogo Silva",
        "email":"diogo.silva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": true,
        "balance": 7002.2
    }
}
```

## Deleta determinado usuário (_soft delete_)

```http
DELETE /api/users/:id
```

__Exemplo__:

```bash
curl -X DELETE http://localhost:8000/api/users/7
```

```json
{
    "status":"deleted",
    "data":{
        "id":7,
        "name":"Diogo Silva",
        "email":"diogo.silva98@hotmail.com",
        "document":"539.064.320-84",
        "logist": true,
        "balance": 7002.2
    }
}
```

## Lista as transferências de determinado usuário

```http
GET /api/transactions/:id
```

__Exemplo__:

```bash
curl http://localhost:8000/api/transactions/7
```

```json
[
    {
        "id": 2,
        "value": 15,
        "date": "18/04/2021 às 19:07:59",
        "payer": 1,
        "payee": 7
    },
    {
        "id": 1,
        "value": 15,
        "date": "19/04/2021 às 18:07:59",
        "payer": 1,
        "payee": 7
    }
]
```

## Realiza uma transferência entre usuários (_payer_ e _payee_)

```http
POST /api/transactions
```

__Exemplo__:

```bash
curl -X POST http://localhost:8000/api/transactions -H "Content-Type: application/json" -d '{"value":100.0,"payee":4,"payer":15}'
```

```json
{
    "status": "success",
    "data": {
        "value" : 100.00,
        "payer" : 4,
        "payee" : 15
    }
}
```

## Sobre

__LinkedIn__: [Thiago Prates](https://www.linkedin.com/in/tsprates/)
