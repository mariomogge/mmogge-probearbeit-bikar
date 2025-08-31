# Project Brief: Bank Account Service

## Overview

Design and implement RESTful API for managing bank accounts in euro. The system should support:

- Creating accounts
- Performing basic transactions:
  - Deposits
  - Withdraws (overdrafts are not allowed — the balance must remain ≥ 0)
  - Viewing the current balance, including a history of transactions

_You do not need to manage currencies or huge amounts for the balance, deposits or withdraws._

## Requirements

- **API**: Provide a RESTful Bank Account Service API.
- **Client**: Include example interactions (e.g., using `curl`, `hurl`, or any tool you prefer).
- **User Authentication**: Ensure each user can only access and manage their own account.

### Optional Enhancements

- **CLI Commands**: Add Symfony commands for account-related tasks.
- **Frontend**: Create a simple frontend interface for interacting with the service.

## Technical Considerations

- **Backend**: Use the latest stable Symfony Framework with PHP 8.4. You’re free to use any Symfony components or bundles you find useful.
- **Security**: Implement proper authentication and protect against common vulnerabilities.
- **Testing**: Include unit tests for core business logic. Integration, end-to-end, or mutation tests are a plus, but not mandatory.

## Submission Guidelines

- Use a local Git repository (you may push to a remote repo if you like).
- Use Docker and Docker Compose for local development.
- Include a `README.md` with setup and usage instructions.
- Document assumptions and key design decisions.
- Use meaningful commit messages.

## Final Thoughts

- This task is less about feature completeness and more about thoughtful implementation.
- Feel free to make the project your own — add more or simplify where you think it makes sense.
- Clean, maintainable code and good documentation are more valuable than perfection.
- And most importantly: have fun with it! 🚀
