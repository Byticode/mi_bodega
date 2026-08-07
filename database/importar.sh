#!/usr/bin/env bash
#
# importar.sh — Restaura la base de datos "mi_bodega" con el esquema correcto
# del proyecto (database/mi_bodega_mvp.sql).
#
# Uso:
#   ./importar.sh            Pide confirmación y restaura la BD
#   ./importar.sh -y         Restaura sin preguntar
#   ./importar.sh -b         Hace un backup antes de restaurar
#   ./importar.sh -h         Muestra esta ayuda
#
# ⚠️ ADVERTENCIA: este script ELIMINA todas las tablas existentes de la BD
# antes de importar. Las credenciales se leen de config/config_local.php
# (igual que la aplicación); si no existe, usa los valores por defecto.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SQL_FILE="$SCRIPT_DIR/mi_bodega_mvp.sql"
CONFIG_FILE="$SCRIPT_DIR/../config/config_local.php"

CONFIRM=1
DO_BACKUP=0

# ---- Opciones ----------------------------------------------------------------
while [[ $# -gt 0 ]]; do
    case "$1" in
        -y | --yes)    CONFIRM=0; shift ;;
        -b | --backup) DO_BACKUP=1; shift ;;
        -h | --help)
            cat <<'EOF'
Restaura la base de datos "mi_bodega" con el esquema de database/mi_bodega_mvp.sql.

Uso:
  ./importar.sh            Pide confirmación y restaura la BD
  ./importar.sh -y         Restaura sin preguntar
  ./importar.sh -b         Hace un backup (database/backup_FECHA.sql) antes de restaurar
  ./importar.sh -h         Muestra esta ayuda

⚠️  ADVERTENCIA: se ELIMINAN todas las tablas existentes de la BD antes de importar.
    Las credenciales se leen de config/config_local.php (si existe).
EOF
            exit 0
            ;;
        *)
            echo "❌ Opción desconocida: $1 (usa -h para ayuda)"
            exit 1
            ;;
    esac
done

# ---- Requisitos ----------------------------------------------------------------
for cmd in mysql mysqldump php; do
    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo "❌ No se encontró el comando '$cmd'. Instálalo para continuar."
        exit 1
    fi
done

if [[ ! -f "$SQL_FILE" ]]; then
    echo "❌ No se encontró el archivo de esquema: $SQL_FILE"
    exit 1
fi

# ---- Credenciales (desde config_local.php si existe) -----------------------------
if [[ -f "$CONFIG_FILE" ]]; then
    # Genera "VAR='valor'" con escape seguro para shell, leyendo las
    # constantes definidas en config_local.php (igual que la aplicación).
    PHP_TMP=$(mktemp)
    cat >"$PHP_TMP" <<'PHPEOF'
<?php
require $argv[1];
$defaults = ["DB_HOST" => "localhost", "DB_NAME" => "mi_bodega", "DB_USER" => "root", "DB_PASS" => ""];
foreach ($defaults as $k => $v) {
    $val = defined($k) ? constant($k) : $v;
    printf("%s='%s'\n", $k, str_replace("'", "'\''", $val));
}
PHPEOF
    eval "$(php "$PHP_TMP" "$CONFIG_FILE")"
    rm -f "$PHP_TMP"
else
    echo "⚠️  No existe config/config_local.php — usando valores por defecto (root sin contraseña)."
    DB_HOST="localhost"
    DB_NAME="mi_bodega"
    DB_USER="root"
    DB_PASS=""
fi

MYSQL_ARGS=(-h "$DB_HOST" -u "$DB_USER")
[[ -n "$DB_PASS" ]] && MYSQL_ARGS+=("-p$DB_PASS")
MYSQL_ARGS+=("$DB_NAME")

# ---- Resumen y confirmación ------------------------------------------------------
echo "=================================================="
echo " Restaurar base de datos '$DB_NAME' en '$DB_HOST'"
echo " Esquema : $SQL_FILE"
echo "=================================================="

CURRENT_TABLES=$(mysql "${MYSQL_ARGS[@]}" -N -e \
    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';" 2>/dev/null || echo "?")
echo " Tablas actuales en la BD: $CURRENT_TABLES"

if [[ "$CONFIRM" == 1 ]]; then
    echo ""
    echo "⚠️  Se ELIMINARÁN todas las tablas de '$DB_NAME' y se recrearán con el esquema correcto."
    read -r -p "¿Continuar? [s/N] " resp
    if [[ ! "$resp" =~ ^[sS]$ ]]; then
        echo "Cancelado."
        exit 0
    fi
fi

# ---- Backup opcional ----------------------------------------------------------------
if [[ "$DO_BACKUP" == 1 ]]; then
    BACKUP_FILE="$SCRIPT_DIR/backup_$(date +%Y%m%d_%H%M%S).sql"
    echo "📦 Creando backup: $BACKUP_FILE ..."
    mysqldump "${MYSQL_ARGS[@]}" >"$BACKUP_FILE"
    echo "✅ Backup creado."
fi

# ---- Eliminar tablas existentes ------------------------------------------------------
echo "🧹 Eliminando tablas existentes ..."
TABLES=$(mysql "${MYSQL_ARGS[@]}" -N -e \
    "SELECT GROUP_CONCAT(CONCAT('\`', table_name, '\`') SEPARATOR ',') FROM information_schema.tables WHERE table_schema='$DB_NAME';")
if [[ -n "$TABLES" ]]; then
    mysql "${MYSQL_ARGS[@]}" -e \
        "SET FOREIGN_KEY_CHECKS=0; DROP TABLE IF EXISTS $TABLES; SET FOREIGN_KEY_CHECKS=1;"
fi

# ---- Importar esquema correcto ---------------------------------------------------------
echo "📥 Importando mi_bodega_mvp.sql ..."
mysql "${MYSQL_ARGS[@]}" <"$SQL_FILE"
echo "✅ Base de datos restaurada con éxito."

# ---- Verificación -----------------------------------------------------------------------
echo "📋 Tablas creadas:"
mysql "${MYSQL_ARGS[@]}" -e "SHOW TABLES;"
echo "📋 Estructura de 'clientes' (debe tener cliente_id, cliente_nombre, ...):"
mysql "${MYSQL_ARGS[@]}" -e "DESCRIBE clientes;"

echo ""
echo "🎉 Listo. El proyecto ahora usa el esquema correcto."
