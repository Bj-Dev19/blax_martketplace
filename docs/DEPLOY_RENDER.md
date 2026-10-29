# Deployment Guide: Render (Docker) + Aiven MySQL

This guide walks you through migrating and deploying the **Blax Marketplace & Inventory** system to Render's Free Docker instance with a free managed MySQL database on Aiven.

---

## 1. Export Database from Laragon

Open Laragon terminal or command prompt:

```bash
# Export the entire database schema and data
mysqldump -u root -p blax_marketplace > blax_inventory_dump.sql
```

> **Aiven MySQL Requirement (sql_require_primary_key)**:
> Aiven enforces primary keys on all tables. If you exported from phpMyAdmin or standard MySQL where primary keys or constraints are appended after `CREATE TABLE`, open `blax_inventory_dump.sql` in a text editor and add this line at the very top:
> ```sql
> SET SESSION sql_require_primary_key = 0;
> ```

---

## 2. Set Up Free Aiven MySQL Database

1. Sign up / Log in to [Aiven Console](https://console.aiven.io/).
2. Create a new service:
   - **Service Type**: MySQL (Free Tier plan)
   - **Cloud Provider & Region**: AWS / Google Cloud (Singapore `ap-southeast-1` recommended for lowest latency to Render Singapore)
   - **Service Name**: `blax-inventory-db`
3. Under the **Overview** tab, wait until status is **Running**.
4. Create database:
   - Under the **Databases** tab, create database: `blax_inventory`
5. Note down your **Connection Information**:
   - **Host**: `mysql-xxxx-your-project.aivencloud.com`
   - **Port**: *Dynamic port number (e.g., 12345 — NOTE: Aiven does NOT use port 3306)*
   - **User**: `avnadmin`
   - **Password**: *(copied from Aiven console)*
   - **Database**: `blax_inventory`

---

## 3. Import SQL Dump into Aiven

Import your dump directly using the MySQL CLI or phpMyAdmin/DBeaver/Navicat:

```bash
mysql -h <AIVEN_HOST> -P <AIVEN_PORT> -u avnadmin -p blax_inventory < blax_inventory_dump.sql
```

---

## 4. Create the Render Web Service

### Option A: Using Render Blueprint (`render.yaml`)
1. Push your repository to GitHub.
2. In [Render Dashboard](https://dashboard.render.com/), click **New +** -> **Blueprint**.
3. Select your repository. Render will automatically detect `render.yaml` and configure the service `blax-marketplace`.
4. Enter the required values marked `sync: false` in the dashboard UI.

### Option B: Manual Web Service Creation
1. Click **New +** -> **Web Service**.
2. Select your GitHub repository (`render-hardening` or `main` branch).
3. Configuration:
   - **Name**: `blax-marketplace`
   - **Region**: Singapore (`singapore`)
   - **Runtime**: Docker
   - **Dockerfile Path**: `./Dockerfile`
   - **Instance Type**: Free
   - **Health Check Path**: `/`

---

## 5. Configure Environment Variables on Render

Under your Web Service -> **Environment**, configure the following variables (refer to `.env.render.example` for details):

| Variable Key | Example / Description | Source |
|---|---|---|
| `CI_ENVIRONMENT` | `production` | Pre-set |
| `APP_BASE_URL` | `https://blax-marketplace.onrender.com/` | Render Web Service URL |
| `DB_HOST` | `mysql-xxxx.aivencloud.com` | Aiven Overview |
| `DB_PORT` | `12345` (Not 3306) | Aiven Overview |
| `DB_USER` | `avnadmin` | Aiven Overview |
| `DB_PASSWORD` | `<your-aiven-password>` | Aiven Overview |
| `DB_DATABASE` | `blax_inventory` | Aiven Databases tab |
| `DB_SSL_CA` | `/var/www/html/docker/certs/aiven-ca.pem` | Aiven Services -> your service -> Connection info (download the service CA and commit it to `docker/certs/aiven-ca.pem`) |
| `DB_SSL_VERIFY` | `true` | Set `false` only to debug a CA mismatch; that keeps encryption but drops server authentication |
| `CLOUDINARY_CLOUD_NAME` | `<your-cloud-name>` | Cloudinary Dashboard |
| `CLOUDINARY_API_KEY` | `<your-api-key>` | Cloudinary Dashboard |
| `CLOUDINARY_API_SECRET` | `<your-api-secret>` | Cloudinary Dashboard |
| `PAYMONGO_PUBLIC_KEY` | `pk_live_...` or `pk_test_...` | PayMongo Developers tab |
| `PAYMONGO_SECRET_KEY` | `sk_live_...` or `sk_test_...` | PayMongo Developers tab |
| `PAYMONGO_WEBHOOK_SECRET`| `whsec_...` | PayMongo Webhook setup |
| `REQUIRE_PAYMONGO_WEBHOOK` | `false` | Boot policy. `false` (default) logs a warning and serves when the webhook secret is missing or a placeholder; webhooks then fail closed with HTTP 400 and paid orders stay unconfirmed until you set the real secret. `true` makes the container refuse to boot instead - only for a deploy where accepting payments is mandatory |
| `BREVO_API_KEY` | `xkeysib-...` | Brevo SMTP & API page |
| `BREVO_SENDER_EMAIL` | `noreply@yourdomain.com` | Brevo Verified Sender |
| `BREVO_SENDER_NAME` | `Blax General Merchandise` | Sender Name |
| `GOOGLE_MAPS_API_KEY` | `AIzaSy...` | Google Cloud Console |
| `COHERE_API_KEY` | `<your-cohere-key>` | Cohere API Keys |
| `TEXTBEE_API_KEY` | `<your-textbee-key>` | TextBee Dashboard |
| `TEXTBEE_DEVICE_ID` | `<your-device-id>` | TextBee Dashboard |
| `TEXTBEE_BASE_URL` | `https://api.textbee.dev/api/v1` | TextBee Default |
| `CACHE_HANDLER` | `database` | Required. The free tier's filesystem is ephemeral, so a restart would otherwise drop the `pending_cart_*` entries the PayMongo webhook reads back |
| `RUN_MIGRATIONS` | `false` | Default. Flip to `true` for exactly one deploy when a migration is pending, then flip it back (see below) |

### Applying migrations

Boot-time migration is off by default, and a failing migration now aborts the deploy
instead of booting onto a half-migrated schema. To apply a pending migration:

1. Run it against the production database yourself:
   `php spark migrate --all` (locally with `DB_*` pointed at Aiven, or in a one-off shell in the Render service).
2. Verify the schema, then temporarily set `RUN_MIGRATIONS: "true"` and deploy once, or skip it entirely if step 1 already applied everything.
3. Set `RUN_MIGRATIONS` back to `"false"`.

---

## 6. Deploy and Monitor Logs

1. Click **Manual Deploy** -> **Deploy latest commit** (or trigger automatically on push).
2. Watch the deployment logs:
   - Nginx boots and listens on the dynamic `${PORT}` injected by Render.
   - PHP-FPM boots with optimized OPcache and `expose_php = Off`.
   - Health check queries `/health`, which is handled by PHP, so the probe fails when the FPM pool is dead. (`/healthz` still exists and is answered by nginx for cheap external pre-warm pings.)

---

## 7. Post-Deployment External Service Updates

### 1. PayMongo Webhooks
In your PayMongo Dashboard -> Developers -> Webhooks:
- Add webhook endpoint: `https://<YOUR-RENDER-URL>/payment/webhook`
- Enable events: `checkout_session.payment.paid`
- Add disbursement webhook (if used): `https://<YOUR-RENDER-URL>/payment/transfer-webhook`
- Copy the signing secret into `PAYMONGO_WEBHOOK_SECRET`.

### 2. Google Maps API Key Restrictions
In Google Cloud Console -> APIs & Services -> Credentials:
- Edit your API Key -> **Set application restrictions** -> **Websites**.
- Add your Render domains:
  - `https://<YOUR-RENDER-URL>/*`
  - `https://<YOUR-CUSTOM-DOMAIN>/*` (if using custom domain)

---

## 8. Render Free Tier Operational Notes

- **Spin Down on Idle**: Render Free instances spin down (sleep) after 15 minutes of inactivity. The initial incoming request incurs a ~50-second cold start while the Docker container starts up.
- **Ephemeral Storage**: Free instances have ephemeral storage. Any file written to the local disk (e.g. `writable/` or local `public/uploads/`) is wiped when the container restarts or redeploys. All production uploads (products, profiles, logos, business permits) must be stored in Cloudinary.
- **Outbound SMTP Ports Blocked**: Outbound traffic on SMTP ports 25, 465, and 587 is blocked on Render Free instances. Transactional emails are dispatched via Brevo's HTTPS API (Port 443), which works reliably across all cloud containers.
