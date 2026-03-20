# QRCompact

Aplicação web full-stack para encurtamento de URLs com QR Code e geração de QR Code Pix.

---

## Escopo

O QRCompact oferece duas funcionalidades principais, cada uma em sua própria página:

| Página | Funcionalidade |
|---|---|
| `/` (`index.html`) | Encurtador de links com QR Code |
| `/pix.html` | Gerador de QR Code e código Pix Copia e Cola |

---

## Stack

- **Frontend:** React 19 + Vite 5
- **Backend:** Node.js + Express
- **Banco de dados:** SQLite (`better-sqlite3`)
- **Geração de QR Code:** biblioteca `qrcode`

---

## Regras de negócio

### 1. Encurtador de links (`/`)

#### Criação de link curto
- A URL informada deve obrigatoriamente usar o protocolo `http://` ou `https://`. Qualquer outro valor é rejeitado com erro `400`.
- O slug é gerado aleatoriamente com **6 caracteres** alfanuméricos (`a-z`, `A-Z`, `0-9`).
- O sistema tenta até **10 vezes** gerar um slug único. Se todas as tentativas colidirem, retorna erro `500`.
- Colisão no banco (`SQLITE_CONSTRAINT_UNIQUE`) retorna erro `409`.
- Ao criar um link, o backend retorna o slug, a URL curta completa e o QR Code em formato `data:image/png;base64,...`.

#### Edição de link curto
- A URL de destino de qualquer slug pode ser atualizada (`PUT /api/links/:slug`).
- A nova URL passa pelas mesmas validações de protocolo (`http://` ou `https://`).
- O slug em si **não pode ser alterado**; apenas a URL de destino.

#### Listagem
- Exibe os últimos **100 links** cadastrados, ordenados do mais recente para o mais antigo.

#### Redirecionamento
- Acessar `/:slug` redireciona o visitante para a URL original com **HTTP 302**.
- Slugs inexistentes retornam HTTP `404` com mensagem de texto simples.
- Requisições cujo segmento inicial seja `api` ou contenha `.` (extensões de arquivo) não são tratadas como slugs e passam para o próximo middleware.

---

### 2. Gerador de QR Code Pix (`/pix.html`)

#### Payload
- O payload segue a especificação **EMV/BR Code** do Banco Central do Brasil (campo `26` com GUI `BR.GOV.BCB.PIX`).
- O CRC de verificação é calculado com o algoritmo **CRC-16/CCITT** (polinômio `0x1021`, valor inicial `0xFFFF`).
- O valor monetário é **opcional**. Quando informado, é incluído no campo `54`; caso contrário, o campo é omitido (QR Code de valor aberto).
- O TXID é sanitizado para apenas caracteres alfanuméricos e limitado a **25 caracteres**. Se não informado, usa o literal `***`.

#### Tipos de chave Pix aceitos
| Tipo | Formatação aplicada |
|---|---|
| Telefone | Adiciona `+55` se não iniciado com `55`; remove não-dígitos |
| CPF | Remove caracteres não numéricos |
| CNPJ | Remove caracteres não numéricos |
| E-mail | Converte para minúsculas |
| Chave aleatória | Enviada sem transformação |

#### Validações obrigatórias
- **Chave Pix:** campo obrigatório; não pode ser vazio após formatação.
- **Nome do beneficiário:** obrigatório; limitado a **25 caracteres** úteis após remoção de acentos e caracteres especiais.
- **Cidade:** obrigatória; limitada a **15 caracteres** úteis após a mesma sanitização.

#### QR Code Pix
- O QR Code é gerado no backend (`POST /api/qr`) com largura de **300 px** e nível de correção de erros **M**.
- Retornado como `data:image/png;base64,...`.
- O código Pix Copia e Cola (string EMV completa com CRC) é disponibilizado para cópia via Clipboard API, com fallback por `execCommand('copy')`.

---

## API REST

| Método | Rota | Descrição |
|---|---|---|
| `POST` | `/api/shorten` | Cria link curto e retorna QR Code |
| `GET` | `/api/links` | Lista os últimos 100 links |
| `GET` | `/api/links/:slug` | Detalha um link e retorna QR Code |
| `PUT` | `/api/links/:slug` | Atualiza a URL de destino de um link |
| `POST` | `/api/qr` | Gera QR Code a partir de qualquer string |
| `GET` | `/:slug` | Redireciona para a URL original (HTTP 302) |

---

## Variáveis de ambiente

| Variável | Padrão | Descrição |
|---|---|---|
| `PORT` | `3000` | Porta em que o servidor escuta |
| `BASE_URL` | `http://localhost:PORT` | Base usada para compor as URLs curtas |

---

## Scripts

```bash
# Desenvolvimento (backend + frontend em paralelo)
npm run dev

# Build do frontend
npm run build

# Produção
npm start
```
