# PHP Support for WORKWAY

This folder adds an optional PHP runtime for the project alongside the existing Node.js + React stack.

## Run the PHP server

```bash
php -S 127.0.0.1:8001 -t php
```

## Available endpoints

- `GET /` – project overview
- `GET /api/health` – health check
- `POST /api/login` – example login endpoint

## Notes

This is a lightweight PHP entry point intended to demonstrate project-level PHP support. The main app continues to run with the Node.js backend.
