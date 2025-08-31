# cURL Requests
## Register
```bash
curl -X POST http://localhost:8080/api/register \
  -H 'Content-Type: application/json' \
  -d '{"email":"alice@example.com","password":"Str0ngP@ss!"}'
```
## Login to get JWT
```bash
curl -X POST http://localhost:8080/api/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"alice@example.com","password":"Str0ngP@ss!"}'
# => {"token":"<JWT>"}
```
## Create account with initial deposit (in cents)
```bash
TOKEN=<JWT>
curl -X POST http://localhost:8080/api/accounts \
  -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"initialDepositCents": 5000}'
# => {"id":"<accountId>","balanceCents":5000}
```

## Deposit 250
```bash
curl -X POST http://localhost:8080/api/accounts/<accountId>/deposit \
  -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"amountCents": 25000}'
```

## Withdraw 50
```bash
curl -X POST http://localhost:8080/api/accounts/<accountId>/withdraw \
  -H "Authorization: Bearer $TOKEN" -H 'Content-Type: application/json' \
  -d '{"amountCents": 5000}'
```

## Show balance
```bash
curl -H "Authorization: Bearer $TOKEN" http://localhost:8080/api/accounts/<accountId>
```

## List transactions
```bash
curl -H "Authorization: Bearer $TOKEN" \
  'http://localhost:8080/api/accounts/<accountId>/transactions?limit=20'
```