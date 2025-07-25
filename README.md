


## Usuários

**admin**

      username: admin
      password: admin

**user**

      username: user
      password: user
---
## Menu


![Menu](.misc/menu.png "Menu")

---

## Classes

```mermaid
  classDiagram
    Answer  <|-- Question
    Vote <|-- Question
    Vote <|-- Answer
    Vote <|-- User
    User
    class Question{
      -string title
      -string identifier
      -string identifier
      -bool show_results
      -bool staus
      +getAnswersCount()
      +getAnswersIds()
      +getAnswers()KK
      +getStatistics()
      +clearStatistics()
      +getAnswerStatistics(int $answerId)K
    }
    class Answer{
      -int question
      -string title
      -int image
      -string description
      +getImageURL()

    }
    class Vote{
      -int question
      -int answer
      -int user
    }
    class User{
      - Drupal User entity-
    }

```
---

## API

**Formato:** JSON

**Postman:** Collection em _.postman/_

#### Autenticação

Autenicação aravés do Header **Authorization**.

Exemplo:

    Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiIxIiwibm...


#### Erro / Exceções

Fomato de Response com erro:

Exemplo de Response:

    {
        "status": "error",
        "exception": "TypeError",
        "message": "Loren ipsun dolor..."
    }


#### Login

Recupera token de acesso.

*Endpoint:*

    POST /api/login

*Parametros (Body Payload):*

- username: string nome do usuário
- password: string senha do usuário

*Exemplo Request Payload:*

    {
      "username": "admin",
      "password": "admin"
    }

*Exemplo Response:*

    {
      "status": "success",
      "time": 1753462565,
      "data": {
          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiIxIiwibmFtZSI6ImFkbWluIiwiZXhwIjoxNzUzNDg0MTY1fQ.iuGJTQEOh4lvh2VG0ujg9l8y-byjWiGWV2IsUHPpJHQ"
      }
    }


#### Profile

Recupera dados do perfil do usuário.

**Requer Autenticação**

*Endpoint:*

    GET /api/profile

*Exemplo Response:*

    {
      "status": "success",
      "time": 1753462568,
      "data": {
          "uid": "1",
          "name": "admin",
          "mail": "admin@example.com",
          "roles": [
              "authenticated",
              "administrator"
          ]
      }
    }

#### Question - Listar

Lista as Questions ativas.

**Requer Autenticação**

*Endpoint:*

    GET /api/question

*Parametros (Query String):*

- page (Opcional): int número da página
- limit (Opcional): int número de registros por página

*Exemplo de Request:*

    /api/question?page=2&limit=5


*Exemplo Response:*

    {
      "status": "success",
      "time": 1753462809,
      "data": {
          "page": 2,
          "per_page": 5,
          "total": 101,
          "total_pages": 21,
          "data": [
              {
                  "identifier": "959dce11b881616e",
                  "title": "Pergunta de teste 5(959dce11b881616e)"
              },
              {
                  "identifier": "d6e3a1e71158a550",
                  "title": "Pergunta de teste 6(d6e3a1e71158a550)"
              },
              {
                  "identifier": "7dd1cea09ad18670",
                  "title": "Pergunta de teste 7(7dd1cea09ad18670)"
              },
              {
                  "identifier": "333717544d5395f8",
                  "title": "Pergunta de teste 8(333717544d5395f8)"
              },
              {
                  "identifier": "2ea2c1124a3af57a",
                  "title": "Pergunta de teste 9(2ea2c1124a3af57a)"
              }
          ]
      }
    }

#### Question - Detalhes

Exibe informações detalhadas da pergunta.

**Requer Autenticação**

*Endpoint:*

    GET /api/question/<identifier>

*Parametros (URL):*

- identifier: string identificador da pergunta

*Exemplo de Request:*

    /api/question/01a38bdb421d5624


*Exemplo Response:*

    {
      "status": "success",
      "time": 1753463540,
      "data": {
          "identifier": "01a38bdb421d5624",
          "title": "Pergunta de teste 0(01a38bdb421d5624)",
          "answers": [
              {
                  "id": "1",
                  "title": "Resposta de teste 0",
                  "description": "Description resposta de teste 0",
                  "imageURL": null
              },
              {
                  "id": "2",
                  "title": "Resposta de teste 1",
                  "description": "Description resposta de teste 1",
                  "imageURL": null
              },
              {
                  "id": "3",
                  "title": "Resposta de teste 2",
                  "description": "Description resposta de teste 2",
                  "imageURL": null
              }
          ]
      }
    }

#### Question - Votar

Permite o registro de voto jpara pergunta.

**Requer Autenticação**

*Endpoint:*

    POST /api/question/<identifier>

*Parametros (URL):*

- identifier: string identificador da pergunta

*Parametros (Body Payload):*

- answer: int identificador da resposta

*Exemplo de Request:*

    /api/question/01a38bdb421d5624

  Exemplo de Payload:

    {
      "answer": 2
    }


*Exemplo Response:*

    {
      "status": "success",
      "time": 1753463879,
      "data": {
          "identifier": "01a38bdb421d5624",
          "title": "Pergunta de teste 0(01a38bdb421d5624)",
          "statistics": {
              "date": "2025-07-25 17:17:59",
              "total": 246,
                  "2": {
                      "votes": 246,
                      "percent": 100,
                      "title": "Resposta de teste 1"
                  }
              }
          }
      }
    }


**statistics** será exibido de acordo com as configurações da pergunta.

---

## Apache Benchmark

Teste:

    ab -n 1000 -c 1000 -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiIxIiwibmFtZSI6ImFkbWluIiwiZXhwIjoxNzUzNDkwMTQ4fQ.15mM0_j9dONonf4paLJ-GodJehnNryW8Bd9AALx-50M" http://vote-app.lndo.site/api/question

Resultado:

    Server Software:        Apache/2.4.62
    Server Hostname:        vote-app.lndo.site
    Server Port:            80

    Document Path:          /api/question
    Document Length:        517 bytes

    Concurrency Level:      1000
    Time taken for tests:   32.922 seconds
    Complete requests:      1000
    Failed requests:        0
    Total transferred:      1010000 bytes
    HTML transferred:       517000 bytes
    Requests per second:    30.37 [#/sec] (mean)
    Time per request:       32922.338 [ms] (mean)
    Time per request:       32.922 [ms] (mean, across all concurrent requests)
    Transfer rate:          29.96 [Kbytes/sec] received

    Connection Times (ms)
                  min  mean[+/-sd] median   max
    Connect:        0   47   9.5     46      64
    Processing:   208 15358 9837.3  14969   32687
    Waiting:      144 15358 9837.4  14967   32685
    Total:        208 15405 9833.9  15002   32720

    Percentage of the requests served within a certain time (ms)
      50%  15002
      66%  20964
      75%  24219
      80%  25471
      90%  29520
      95%  30660
      98%  31995
      99%  32069
    100%  32720 (longest request)

---

## Tecnologias

- Lando
- Drupal
- Postman
- Apache
- Redis
- Bugsnag
