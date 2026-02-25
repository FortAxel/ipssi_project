#!/bin/bash

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${BLUE}"
echo "╔════════════════════════════════════════╗"
echo "║   Générateur PDF - Projet CDA 2026    ║"
echo "╚════════════════════════════════════════╝"
echo -e "${NC}"

# Vérifier les dépendances
check_dep() {
    if ! command -v "$1" &> /dev/null; then
        echo -e "${RED}❌ '$1' n'est pas installé.${NC}"
        exit 1
    fi
}
check_dep pandoc

# Dossier du script (là où sont template.tex et metadata.yaml)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TEMPLATE="$SCRIPT_DIR/cda-template.tex"
METADATA="$SCRIPT_DIR/metadata.yaml"

if [ ! -f "$TEMPLATE" ]; then
    echo -e "${RED}❌ Template introuvable : $TEMPLATE${NC}"
    exit 1
fi
if [ ! -f "$METADATA" ]; then
    echo -e "${RED}❌ Metadata introuvable : $METADATA${NC}"
    exit 1
fi

# Déterminer le fichier source
if [ $# -eq 1 ]; then
    INPUT="$1"
    [[ "$INPUT" != /* ]] && INPUT="$(pwd)/$INPUT"
else
    cd "$SCRIPT_DIR"
    mapfile -t MD_FILES < <(find . -name "*.md" -type f | sort)

    if [ ${#MD_FILES[@]} -eq 0 ]; then
        echo -e "${RED}❌ Aucun fichier .md trouvé dans $SCRIPT_DIR${NC}"
        exit 1
    fi

    echo -e "${YELLOW}📁 Fichiers Markdown disponibles :${NC}"
    for i in "${!MD_FILES[@]}"; do
        echo "  $((i+1)). ${MD_FILES[$i]#./}"
    done

    echo ""
    read -rp "$(echo -e "${CYAN}Choisir un numéro : ${NC}")" choice

    if ! [[ "$choice" =~ ^[0-9]+$ ]] || [ "$choice" -lt 1 ] || [ "$choice" -gt ${#MD_FILES[@]} ]; then
        echo -e "${RED}❌ Choix invalide${NC}"
        exit 1
    fi

    INPUT="$SCRIPT_DIR/${MD_FILES[$((choice-1))]#./}"
fi

if [ ! -f "$INPUT" ]; then
    echo -e "${RED}❌ Fichier introuvable : $INPUT${NC}"
    exit 1
fi

OUTPUT="${INPUT%.md}.pdf"
INPUT_DIR=$(dirname "$INPUT")
INPUT_FILE=$(basename "$INPUT")
OUTPUT_FILE=$(basename "$OUTPUT")

echo ""
echo -e "${BLUE}📄 Source  : ${NC}${INPUT_FILE}"
echo -e "${BLUE}📄 Sortie  : ${NC}${OUTPUT_FILE}"
echo -e "${BLUE}📁 Dossier : ${NC}${INPUT_DIR}"
echo ""
echo -e "${BLUE}🔄 Génération du PDF...${NC}"
echo ""

cd "$INPUT_DIR" || exit 1

# Génération du PDF
# --template    : notre template LaTeX custom (mise en page, couleurs, page de titre)
# --metadata-file : métadonnées communes (fontes, marges, etc.)
# Le front matter YAML du .md prend le dessus sur metadata.yaml pour title/author/date
pandoc "$INPUT_FILE" \
    -o "$OUTPUT_FILE" \
    --template="$TEMPLATE" \
    --metadata-file="$METADATA" \
    --pdf-engine=xelatex \
    --highlight-style=tango \
    2>&1

STATUS=$?

echo ""
if [ $STATUS -eq 0 ] && [ -f "$OUTPUT_FILE" ]; then
    SIZE=$(du -h "$OUTPUT_FILE" | cut -f1)
    PAGES=$(pdfinfo "$OUTPUT_FILE" 2>/dev/null | awk '/^Pages:/ {print $2}')
    [ -z "$PAGES" ] && PAGES="?"

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo -e "${GREEN}✅ PDF généré avec succès !${NC}"
    echo ""
    echo -e "  📄 Fichier : ${GREEN}$OUTPUT_FILE${NC}"
    echo -e "  📊 Taille  : ${GREEN}$SIZE${NC}"
    echo -e "  📑 Pages   : ${GREEN}$PAGES${NC}"
    echo -e "  📁 Chemin  : ${GREEN}$(pwd)/$OUTPUT_FILE${NC}"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""

    read -rp "$(echo -e "${CYAN}Ouvrir le PDF ? (o/n) : ${NC}")" open_choice
    if [[ "$open_choice" =~ ^[oO]$ ]]; then
        if [[ "$OSTYPE" == "linux-gnu"* ]]; then
            xdg-open "$OUTPUT_FILE" 2>/dev/null &
        elif [[ "$OSTYPE" == "darwin"* ]]; then
            open "$OUTPUT_FILE"
        fi
        echo -e "${GREEN}📖 Ouverture du PDF...${NC}"
    fi

    echo ""
    echo -e "${GREEN}✨ Terminé !${NC}"
    exit 0
else
    echo ""
    echo -e "${RED}❌ Erreur lors de la génération du PDF (code $STATUS)${NC}"
    echo -e "${YELLOW}💡 Vérifiez les messages d'erreur ci-dessus.${NC}"
    exit 1
fi