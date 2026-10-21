#!/bin/bash
# Uji koleksi Postman apa adanya (newman) terhadap DB scratch hasil fresh seed.
# Usage: bash _e2e.sh asis|log
set -u
MODE="${1:-asis}"
cd /d/documents/coding/university/tnks/web-etiket-gunung-kerinci
PHP=/d/php/php
DB=gunung_kerinci_e2e
BASE=http://127.0.0.1:8001/api
OUT=_newman_$MODE

echo "### mode=$MODE"

# pastikan port 8001 kosong
PID=$(netstat -ano | grep ':8001' | grep LISTENING | awk '{print $NF}' | head -1)
if [ -n "${PID:-}" ]; then taskkill /F /PID "$PID" >/dev/null 2>&1; sleep 1; fi

$PHP _db.php drop $DB
$PHP _db.php create $DB || exit 1

echo "### migrate:fresh --seed"
DB_DATABASE=$DB $PHP artisan migrate:fresh --seed --force 2>&1 | tail -3

if [ "$MODE" = "log" ]; then
  export MAIL_MAILER=log
  echo "### mailer=log (override .env)"
fi

DB_DATABASE=$DB $PHP artisan serve --host=127.0.0.1 --port=8001 > _srv_$MODE.log 2>&1 &
for i in $(seq 1 40); do
  code=$(curl -s -o /dev/null -w '%{http_code}' --max-time 2 http://127.0.0.1:8001/ || true)
  if [ "$code" = "302" ] || [ "$code" = "200" ]; then break; fi
  sleep 1
done
echo "### server ready (http=$code)"

echo "### newman run"
npx -y newman run _tnks.json \
  --env-var base_url=$BASE \
  --timeout-request 60000 \
  --reporters cli,json \
  --reporter-json-export ${OUT}.json > ${OUT}.txt 2>&1
echo "### newman exit=$?"

PID=$(netstat -ano | grep ':8001' | grep LISTENING | awk '{print $NF}' | head -1)
if [ -n "${PID:-}" ]; then taskkill /F /PID "$PID" >/dev/null 2>&1; fi
echo "### done"
