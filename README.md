# QRCompact

Aplicacao Laravel 12 para encurtamento de URLs com QR Code e geracao de Pix copia e cola com QR Code Pix, pronta para uso local com SQLite e preparada para deploy na Hostinger via hPanel.

## Escopo

O QRCompact entrega duas areas principais:

- Links Curtos em `/`: cria slugs aleatorios de 6 caracteres, gera QR Code da URL curta, lista os ultimos 100 links e permite editar o destino por slug.
- Gerar Pix em `/pix`: monta o payload EMV/BR Code no backend, calcula CRC16-CCITT e devolve QR Code Pix junto com o codigo copia e cola.

## Stack

- PHP ^8.3
- Laravel 12
- SQLite por padrao em `database/database.sqlite`
- Suporte simples a MySQL via `.env`
- Blade + Vite + JavaScript puro
- QR Code no backend com `endroid/qr-code`

## Regras de negocio

### Encurtador

- A URL deve ser valida e obrigatoriamente iniciar com `http://` ou `https://`.
- O slug e alfanumerico com exatamente 6 caracteres.
- O sistema tenta gerar um slug unico ate 10 vezes.
- Os links sao persistidos na tabela `links` com `id`, `slug`, `original_url`, `created_at` e `updated_at`.
- A listagem retorna os ultimos 100 links do mais recente para o mais antigo.
- O destino pode ser editado por slug, sem alterar o slug.
- `GET /{slug}` faz redirecionamento `302` para a URL original.
- Quando o slug nao existe, a resposta e `404` com a mensagem `Link curto nao encontrado`.

### Pix

- O payload segue EMV/BR Code com GUI `BR.GOV.BCB.PIX`.
- Tipos de chave suportados: `phone`, `cpf`, `cnpj`, `email`, `random`.
- `phone`: remove nao digitos e adiciona `+55` quando necessario.
- `cpf` e `cnpj`: mantem somente numeros.
- `email`: converte para minusculas.
- `random`: mantem a chave como foi enviada.
- Nome do beneficiario e obrigatorio, sanitizado e limitado a 25 caracteres.
- Cidade e obrigatoria, sanitizada e limitada a 15 caracteres.
- Valor e opcional; quando maior que zero, entra no campo `54` com 2 casas decimais.
- TXID aceita apenas caracteres alfanumericos, limitado a 25, com padrao `***`.
- O payload e finalizado com CRC16-CCITT.

## Rotas e endpoints

### Paginas

- `GET /` tela de Links Curtos
- `GET /pix` tela de Gerar Pix
- `GET /{slug}` redirecionamento 302 para a URL original

### API

- `POST /api/shorten` cria link curto e retorna QR Code
- `GET /api/links` lista os ultimos 100 links
- `GET /api/links/{slug}` detalha um link e retorna QR Code
- `PUT /api/links/{slug}` atualiza o destino do link
- `POST /api/qr` gera QR Code generico ou payload Pix com QR Code

### Corpo esperado em `POST /api/qr`

QR generico:

```json
{
	"data": "https://seusite.com"
}
```

Pix:

```json
{
	"mode": "pix",
	"key_type": "phone",
	"key": "(11) 99999-9999",
	"name": "Jose da Silva",
	"city": "Sao Paulo",
	"amount": "19,90",
	"txid": "PEDIDO123"
}
```

## Estrutura principal

- `app/Http/Controllers/PageController.php` paginas Blade
- `app/Http/Controllers/Api/LinkController.php` API do encurtador
- `app/Http/Controllers/Api/QrCodeController.php` QR generico e Pix
- `app/Http/Controllers/RedirectController.php` redirecionamento por slug
- `app/Http/Requests` validacoes HTTP
- `app/Services/LinkService.php` regra de negocio do encurtador
- `app/Services/QrCodeService.php` geracao de QR no backend
- `app/Services/PixPayloadService.php` montagem do payload Pix
- `app/Models/Link.php` modelo Eloquent
- `database/migrations/2026_03_21_000003_create_links_table.php` tabela `links`
- `resources/views` telas Blade
- `resources/js/app.js` comportamento do frontend
- `resources/css/app.css` estilos da interface

## Setup local

### Requisitos

- PHP 8.3+
- Composer 2+
- Node.js 20+
- Extensoes PHP: `pdo_sqlite`, `sqlite3`, `gd`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `ctype`, `json`

### Passo a passo

1. Instale as dependencias PHP.

```bash
composer install
```

2. Instale as dependencias front-end.

```bash
npm install
```

3. Crie o arquivo `.env` a partir do exemplo e ajuste o ambiente local se necessario.

```bash
copy .env.example .env
```

4. Gere a chave da aplicacao.

```bash
php artisan key:generate
```

5. Garanta que o SQLite exista.

```bash
type nul > database\\database.sqlite
```

6. Rode as migrations.

```bash
php artisan migrate
```

7. Gere os assets ou rode em modo desenvolvimento.

```bash
npm run build
```

ou

```bash
npm run dev
```

8. Suba o servidor local.

```bash
php artisan serve
```

## Configuracao de banco

### SQLite padrao

Use no `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### MySQL

Troque no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qrcompact
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

## Testes

Testes de feature incluidos:

- criacao de link curto
- redirecionamento por slug
- edicao de destino
- geracao de payload Pix com QR Code

Execute:

```bash
php artisan test
```

## Deploy na Hostinger via hPanel

### Passo a passo explicito

1. No hPanel, configure o dominio ou subdominio do projeto.
2. Ajuste a versao do PHP para `8.3`.
3. Defina o document root do dominio para a pasta `public` da aplicacao.
4. Ative as extensoes necessarias: `pdo_sqlite`, `sqlite3`, `gd`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `ctype`, `json`.
5. Envie os arquivos da aplicacao para o servidor, preservando a estrutura do Laravel.
6. No terminal da Hostinger ou via SSH, rode:

```bash
composer install --no-dev --optimize-autoloader
```

7. Gere a chave da aplicacao:

```bash
php artisan key:generate
```

8. Crie o banco SQLite padrao:

```bash
mkdir -p database
touch database/database.sqlite
```

9. Ajuste as permissoes de escrita.
10. Rode as migrations em producao:

```bash
php artisan migrate --force
```

11. Gere os assets de producao.

No servidor, se Node estiver disponivel:

```bash
npm ci
npm run build
```

Se Node nao estiver disponivel no servidor, execute localmente e envie a pasta `public/build` junto com `public/build/manifest.json`.

12. Otimize caches do Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Checklist de permissoes na Hostinger

Os caminhos abaixo precisam ser gravaveis pelo PHP:

- `storage/*`
- `bootstrap/cache`

### Observacoes importantes de roteamento

- As paginas ficam em `routes/web.php`.
- A API fica em `routes/api.php`.
- O redirect do slug usa regex de 6 caracteres e foi declarado para nao conflitar com `/pix` e com o prefixo `/api`.

## `.env.example` para producao

O projeto ja entrega `.env.example` pronto para base produtiva com:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://SEU_DOMINIO
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Exemplo comentado para MySQL tambem ja esta incluido no arquivo.

## Troubleshooting na Hostinger

### Erro 500 por `APP_KEY` ausente

Sintoma: erro 500 logo ao abrir o site.

Correcao:

```bash
php artisan key:generate
```

### Problema de permissao em `storage`

Sintoma: cache, logs, sessoes ou views compiladas falham.

Verifique se estes caminhos estao gravaveis:

- `storage/*`
- `bootstrap/cache`

### Assets nao carregando

Sintoma: layout sem CSS/JS ou erro 404 em arquivos de `build`.

Checklist:

- confirme `APP_URL` correto no `.env`
- confirme que `public/build` foi enviado ao servidor
- confirme que o document root aponta para `public`

### Slug capturando rota indevida

Sintoma: `/pix` ou alguma rota esperada cai no redirect do slug.

Checklist:

- mantenha as paginas declaradas antes da rota `/{slug}` em `routes/web.php`
- mantenha a regex do slug com exatamente 6 caracteres
- mantenha a API em `routes/api.php`

## Checklist final pos-deploy

- dominio aponta para `public`
- PHP 8.3 ativo
- extensoes PHP ativas, incluindo `gd`
- `.env` configurado com `APP_URL` correto
- `APP_KEY` gerado
- `database/database.sqlite` criado ou MySQL configurado
- migrations executadas com `--force`
- assets de `public/build` presentes
- `storage/*` e `bootstrap/cache` com escrita
- `php artisan config:cache`, `route:cache` e `view:cache` executados
- `/` carregando a tela de Links Curtos
- `/pix` carregando a tela de Gerar Pix
- `/api/links` respondendo
- `/{slug}` redirecionando corretamente

## Comandos uteis

```bash
php artisan serve
php artisan migrate
php artisan test
npm run dev
npm run build
```
