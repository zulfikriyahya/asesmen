#!/bin/bash

# =============================================================
# generate.sh
# Dump seluruh isi file views ke satu file markdown
# Usage: bash generate.sh [output_file]
# =============================================================

BASE_DIR="application/models_decoded"
OUTPUT="${1:-draft-models.md}"

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



# # Skills & Project Convention Reference

# Dokumen ini digunakan sebagai konteks bagi AI assistant saat membantu pengembangan project **MTsN 1 Pandeglang**. Baca dokumen ini sebelum memberikan saran kode atau arsitektur.

# ---

# ## Developer Profile

# - **Nama**: Yahya Zulfikri
# - **Level**: Senior Developer
# - **Bahasa**: Indonesia (penjelasan), English (kode & nama teknis)
# - **Asumsi**: Sudah paham konsep dasar seperti instalasi, struktur folder standar, cara kerja framework — tidak perlu dijelaskan ulang kecuali diminta

# ## Aturan
# - Jangan gunakan komentar berlebihan
# - jangan gunakan emoticon
# - jadikan clean code tanpa merubah struktur project
# - refactor kode menjadi responsive, modern, elegan, rapi, dan simetris, menggunakan darkmode dan glassmorpism, Juga gunakan Font Lexend, tanpa merubah logic apapun yang sudah ada.
# - konsistenkan tema dan penulisan kode
# - berikan saya full kode refactor perfile.
# - berikan lokasi file yang diubah
# - tolong perhatikan jarak dan gunakan tema warna cyan, biru atau hijau.
