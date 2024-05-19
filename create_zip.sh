#!/bin/bash

# Setze den Namen des ZIP-Archivs gleich dem Ordnernamen (basierend auf dem aktuellen Verzeichnis)
ARCHIVE_NAME=$(basename "$(pwd)")

# Definiere die Dateien und Verzeichnisse, die ausgeschlossen werden sollen
EXCLUDE=(
    "composer.json"
    "composer.lock"
    "create_zip.sh"
    "vue-dev-ts/*"
    "node_modules/*"
    "test/*"
    "tests/*"
    "tmp/*"
    ".git/*"
    ".idea/*"
    ".DS_Store"
    "build/*"
    "update.json"
    ".gitattributes"
    ".gitignore"
)

  if [ -d "build" ]; then
    rm -rf "build";
  fi

  if [ ! -d "build" ]; then
    mkdir -p "build";
  fi

# Erstelle das ZIP-Archiv, speichere es im übergeordneten Verzeichnis
zip -r "build/${ARCHIVE_NAME}.zip" . -x "${EXCLUDE[@]}"

echo "ZIP-Archiv wurde erstellt und befindet sich in $(dirname "$(pwd)")/${ARCHIVE_NAME}.zip"
