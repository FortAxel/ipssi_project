"""Réaligne les tableaux à grille du dossier de projet.

Certains tableaux ont besoin de largeurs de colonnes imposées : sans elles, les
cellules longues sortent de la zone de texte. Le format à grille les fournit,
mais le formatage automatique de l'éditeur retire les espaces de remplissage des
lignes de contenu sans toucher aux lignes de séparation, ce qui désaligne les
barres verticales et rend le tableau illisible pour Pandoc.

Ce script relit les largeurs dans la ligne de séparation et redonne aux cellules
leur remplissage. Il est appelé par build.sh avant la conversion.
"""

import sys


def column_widths(rule):
    """Largeurs intérieures des colonnes, lues sur une ligne de séparation."""
    bounds = [i for i, c in enumerate(rule) if c == '+']
    return [bounds[i + 1] - bounds[i] - 3 for i in range(len(bounds) - 1)]


def realign(row, widths):
    cells = row.split('|')[1:-1]
    if len(cells) != len(widths):
        return None
    out = '|'
    for cell, width in zip(cells, widths):
        out += ' ' + cell.strip().ljust(width) + ' |'
    return out


def normalize(path):
    lines = open(path, encoding='utf-8').read().split('\n')
    changed = 0
    i = 0
    while i < len(lines):
        if not lines[i].startswith('+--'):
            i += 1
            continue
        widths = column_widths(lines[i])
        j = i + 1
        while j < len(lines) and lines[j][:1] in ('|', '+'):
            if lines[j].startswith('|'):
                fixed = realign(lines[j], widths)
                if fixed is not None and fixed != lines[j]:
                    lines[j] = fixed
                    changed += 1
            j += 1
        i = j

    if changed:
        open(path, 'w', encoding='utf-8').write('\n'.join(lines))
    return changed


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'dossier-projet.md'
    count = normalize(target)
    print(f'tableaux : {count} ligne(s) réalignée(s)')
