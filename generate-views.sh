#!/bin/bash

# =============================================================
# generate-views.sh
# Dump seluruh isi file views ke satu file markdown
# Usage: bash generate-views.sh [output_file]
# =============================================================

BASE_DIR="application/views"
OUTPUT="${1:-draft-views.md}"

# Pastikan BASE_DIR ada
if [ ! -d "$BASE_DIR" ]; then
  echo "❌ Directory '$BASE_DIR' tidak ditemukan. Jalankan dari root project."
  exit 1
fi

> "$OUTPUT"  # kosongkan / buat file output

echo "📁 Scanning: $BASE_DIR"
echo "📝 Output  : $OUTPUT"
echo ""

count=0

# find semua .php, sorted
while IFS= read -r filepath; do
  # Relative path dari root project
  rel_path="${filepath}"

  echo "## File: ${rel_path}" >> "$OUTPUT"
  echo "" >> "$OUTPUT"
  echo '```php' >> "$OUTPUT"
  cat "$filepath" >> "$OUTPUT"
  echo "" >> "$OUTPUT"
  echo '```' >> "$OUTPUT"
  echo "" >> "$OUTPUT"
  echo "---" >> "$OUTPUT"
  echo "" >> "$OUTPUT"

  echo "  ✅ ${rel_path}"
  ((count++))
done < <(find "$BASE_DIR" -type f -name "*.php" | sort)

echo ""
echo "✔ Selesai. $count file ditulis ke '$OUTPUT'."
