#!/bin/bash
# Génère dossier-projet.pdf.
#
# Le script docs/generate-pdf.sh du dépôt utilise xelatex et les polices Liberation,
# absents de cette machine. On passe donc par tectonic et les polices système ;
# les réglages de mise en page (polices, marges, interligne) sont dans le front
# matter de dossier-projet.md.

set -euo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")"

python3 normalize-tables.py dossier-projet.md

pandoc dossier-projet.md \
    -o dossier-projet.pdf \
    --template=../cda-template.tex \
    --metadata-file=../metadata.yaml \
    --include-in-header=toc-style.tex \
    --pdf-engine=tectonic \
    --highlight-style=tango

# Le référentiel plafonne le corps à 60 pages hors page de garde, sommaire et
# annexes, et les annexes à 40. On mesure les deux à partir du fichier .toc
# produit par LaTeX, qui donne la page de début de chaque titre.
python3 - <<'PY'
import re, subprocess
from pypdf import PdfReader

total = len(PdfReader('dossier-projet.pdf').pages)

subprocess.run(['pandoc', 'dossier-projet.md', '-o', '_pages.tex',
                '--template=../cda-template.tex', '--metadata-file=../metadata.yaml',
                '--include-in-header=toc-style.tex', '--highlight-style=tango',
                '--standalone'], check=True, capture_output=True)
subprocess.run(['tectonic', '--keep-intermediates', '_pages.tex'], capture_output=True)

entries = re.findall(
    r'\\contentsline \{subsection\}\{(?:\\numberline \{[^}]*\})?(.*?)\}\{(\d+)\}',
    open('_pages.toc', encoding='utf-8').read())
clean = [(re.sub(r'\\[a-zA-Z]+\s*|[{}]', '', t), int(p)) for t, p in entries]
annexes = next(p for t, p in clean if t.startswith('Annexes'))

corps = annexes - 2
annexes_pages = total - annexes + 1
flag = lambda n, m: 'OK' if n <= m else 'DEPASSEMENT'
print(f"PDF généré : {total} pages")
print(f"  corps   : {corps} / 60   {flag(corps, 60)}")
print(f"  annexes : {annexes_pages} / 40   {flag(annexes_pages, 40)}")

for f in ('_pages.tex', '_pages.toc', '_pages.aux', '_pages.out', '_pages.pdf', '_pages.log'):
    subprocess.run(['rm', '-f', f])
PY
