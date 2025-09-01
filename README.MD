# Bank Account Service (Symfony, PHP 8.4)

A minimal, secure REST API for Euro accounts: create, deposit, withdraw, and view balances/transactions. Auth via JWT.

## Stack
- Symfony (latest 7.x)
- PHP 8.4 (FPM, Docker)
- PostgreSQL 16
- Doctrine ORM 3
- LexikJWT for JWT auth

## Environment
Copy ``.env`` to ``.env.local`` and adjust secrets as needed.
```bash
APP_SECRET=... # random 32+ chars
JWT_PASSPHRASE=changeit
DATABASE_URL=postgresql://bank:bank@db:5432/bank?serverVersion=16&charset=utf8
```

## Quickstart
```bash
make up # start containers
make install # install dependencies
make keys # generate JWT keypair (uses $JWT_PASSPHRASE)
make migrate # create DB schema
make tests # run unit tests via PHPUnit
```
Open http://localhost:8080 and see <a href="CURL_REQUESTS.MD">CURL_REQUESTS.MD</a> for example cURL requests

## CLI Commands (optional)
- ``app:account:open <email> [initialCents]``
- ``app:account:deposit <accountId> <cents>``
- ``app:account:withdraw <accountId> <cents>``

## Security
- JWT protected ``/api/*`` endpoints
- Stateless firewall, CORS via Nelmio
- Users operate only on their own accounts (Voter)

## Migrations
Generate and run:

``docker compose exec php php bin/console make:migration
make migrate``

## Notes
- Endpoints return cents; convert on the client as needed
- Extend ``Transaction`` getters/serialization for richer history responses.

## SOLID & Clean Code Highlights
- **Single Responsibility**: Entities model state; `AccountService` performs use cases; controllers handle HTTP only; repositories persist.
- **Open/Closed**: Add new transaction types via `TransactionType` and service methods without modifying core invariants.
- **Liskov Substitution**: Program to interfaces (`AccountServiceInterface`).
- **Interface Segregation**: Narrow DTOs and service contract.
- **Dependency Inversion**: Controllers depend on `AccountServiceInterface` (configure alias below if preferred).


> If you want the container to inject the interface type, add in `config/services.yaml`:


```yaml
services:
App\Domain\Account\AccountServiceInterface: '@App\Application\Service\AccountService'
