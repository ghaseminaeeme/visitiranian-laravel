#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOCAL="$ROOT/.local"
BASE="$LOCAL/mariadb-10.11.14-linux-systemd-x86_64"
DATA="$LOCAL/mariadb-data"
SOCKET="$LOCAL/mysql.sock"
PIDFILE="$LOCAL/mariadb.pid"
LOG="$LOCAL/mariadb.log"
MYSQL="$BASE/bin/mariadb"
MYSQLD="$BASE/bin/mariadbd"

start() {
  if [[ ! -x "$MYSQLD" ]]; then
    echo "Local MariaDB binary not found. Run: $0 install" >&2
    exit 1
  fi

  if [[ -f "$PIDFILE" ]] && kill -0 "$(cat "$PIDFILE")" 2>/dev/null; then
    echo "MariaDB already running (pid $(cat "$PIDFILE"))."
    return 0
  fi

  rm -f "$PIDFILE"
  nohup "$MYSQLD" \
    --datadir="$DATA" \
    --port=3306 \
    --socket="$SOCKET" \
    --bind-address=127.0.0.1 \
    --pid-file="$PIDFILE" \
    --log-error="$LOG" >>"$LOG" 2>&1 &
  disown

  for _ in $(seq 1 30); do
    if "$MYSQL" -h 127.0.0.1 -P 3306 -u root -e "SELECT 1" >/dev/null 2>&1; then
      echo "MariaDB started (pid $(cat "$PIDFILE"))."
      return 0
    fi
    sleep 1
  done

  echo "MariaDB failed to start. See $LOG" >&2
  exit 1
}

stop() {
  if [[ -f "$PIDFILE" ]] && kill -0 "$(cat "$PIDFILE")" 2>/dev/null; then
    kill "$(cat "$PIDFILE")"
    rm -f "$PIDFILE"
    echo "MariaDB stopped."
  else
    echo "MariaDB is not running."
  fi
}

status() {
  if [[ -f "$PIDFILE" ]] && kill -0 "$(cat "$PIDFILE")" 2>/dev/null; then
    echo "MariaDB running (pid $(cat "$PIDFILE"))."
    "$MYSQL" -h 127.0.0.1 -P 3306 -u root -e "SELECT VERSION() AS version;"
  else
    echo "MariaDB is not running."
    exit 1
  fi
}

import_schema() {
  start
  sed 's/h399366_visitiranianDb/visitiranian/g' "$ROOT/database/sql/schema.sql" | \
    "$MYSQL" -h 127.0.0.1 -P 3306 -u root
  echo "Schema imported into visitiranian."
}

import_procedures() {
  start
  sed 's/h399366_visitiranianDb/visitiranian/g' "$ROOT/database/sql/procedures.sql" | \
    "$MYSQL" -h 127.0.0.1 -P 3306 -u root visitiranian
  echo "Procedures imported."
}

import_laravel_tables() {
  start
  "$MYSQL" -h 127.0.0.1 -P 3306 -u root visitiranian < "$ROOT/database/sql/laravel-tables.sql"
  echo "Laravel cache/queue tables ready."
}

setup() {
  install
  start
  import_schema
  import_laravel_tables
  import_procedures
  echo "Local database setup complete (visitiranian)."
}

install() {
  mkdir -p "$LOCAL"
  if [[ ! -f "$LOCAL/mariadb.tar.gz" ]]; then
    curl -fL --progress-bar -o "$LOCAL/mariadb.tar.gz" \
      "https://downloads.mariadb.com/MariaDB/mariadb-10.11.14/bintar-linux-systemd-x86_64/mariadb-10.11.14-linux-systemd-x86_64.tar.gz"
  fi
  if [[ ! -d "$BASE" ]]; then
    tar -xzf "$LOCAL/mariadb.tar.gz" -C "$LOCAL"
  fi
  if [[ ! -d "$DATA/mysql" ]]; then
    "$BASE/scripts/mariadb-install-db" \
      --datadir="$DATA" \
      --auth-root-authentication-method=normal \
      --skip-test-db
  fi
  echo "Local MariaDB installed under $LOCAL"
}

case "${1:-start}" in
  start) start ;;
  stop) stop ;;
  restart) stop || true; start ;;
  status) status ;;
  import) import_schema ;;
  import-procedures) import_procedures ;;
  import-laravel) import_laravel_tables ;;
  setup) setup ;;
  install) install ;;
  *)
    echo "Usage: $0 {install|setup|start|stop|restart|status|import|import-laravel|import-procedures}" >&2
    exit 1
    ;;
esac
