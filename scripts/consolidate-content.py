#!/usr/bin/env python3
"""
Select 5 French stories (complete image sets), copy to backend/data/stories
and backend/public/images, then delete bulk stories/ and images/ at repo root.

Committed paths (for git):
  backend/data/stories/*.json
  backend/public/images/*.jpg

Run: python3 scripts/consolidate-content.py
"""

from __future__ import annotations

import json
import re
import shutil
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SOURCE_STORIES = ROOT / "stories"
SOURCE_IMAGES = ROOT / "images"
TARGET_STORIES = ROOT / "backend" / "data" / "stories"
TARGET_IMAGES = ROOT / "backend" / "public" / "images"
MAX_STORIES = 5

FRENCH_RE = re.compile(
    r"[àâäéèêëïîôùûüçœæ]|"
    r"\b(le|la|les|un|une|et|est|dans|pour|avec|sur|petit|petite|histoire)\b",
    re.I,
)
ENGLISH_RE = re.compile(r"\b(the|and|was|were|with|his|her|little|one day)\b", re.I)

# Preferred titles when multiple exports exist (e.g. two "Noé" stories)
PREFERRED_FILES = [
    "story-db9e4d2a-3a8c-4c06-8c8c-5cc54e30e166.json",
    "story-eb7b925b-4b56-4ef7-b97b-b88dec14620c.json",
    "story-ccb75d75-936c-4d0d-962a-d0cf452bd6b9.json",
    "story-4630c271-669a-4c2f-814b-46589f02e556.json",
    "story-15697e9f-7b1d-4c9b-a8fc-80e465097b33.json",
]


def is_french(story: dict) -> bool:
    blob = f"{story.get('title', '')} {story.get('summary', '')}"
    fr = len(FRENCH_RE.findall(blob))
    en = len(ENGLISH_RE.findall(blob))
    return fr >= 3 and fr > en


def image_filename(url: str) -> str:
    return url.strip().split("/")[-1]


def story_image_names(story: dict) -> list[str]:
    return [image_filename(i["url"]) for i in story.get("images", [])]


def is_complete(path: Path) -> bool:
    data = json.loads(path.read_text(encoding="utf-8"))
    story = data.get("story", {})
    if not is_french(story):
        return False
    names = story_image_names(story)
    return bool(names) and all((SOURCE_IMAGES / n).exists() for n in names)


def select_stories() -> list[Path]:
    selected: list[Path] = []
    used: set[str] = set()

    for name in PREFERRED_FILES:
        path = SOURCE_STORIES / name
        if path.exists() and is_complete(path):
            selected.append(path)
            used.add(name)

    if len(selected) >= MAX_STORIES:
        return selected[:MAX_STORIES]

    for path in sorted(SOURCE_STORIES.glob("story-*.json")):
        if path.name in used:
            continue
        if is_complete(path):
            selected.append(path)
            used.add(path.name)
        if len(selected) >= MAX_STORIES:
            break

    return selected


def main() -> None:
    if not SOURCE_STORIES.is_dir() or not SOURCE_IMAGES.is_dir():
        raise SystemExit(
            "Place raw export in stories/ and images/ at repo root, then run this script.",
        )

    selected = select_stories()
    if len(selected) < MAX_STORIES:
        raise SystemExit(
            f"Only {len(selected)} complete French stories found; need {MAX_STORIES}.",
        )

    needed_images: set[str] = set()
    TARGET_STORIES.mkdir(parents=True, exist_ok=True)
    TARGET_IMAGES.mkdir(parents=True, exist_ok=True)

    for old_json in TARGET_STORIES.glob("story-*.json"):
        old_json.unlink()
    for old_jpg in TARGET_IMAGES.glob("*.jpg"):
        old_jpg.unlink()

    manifest_entries = []
    for path in selected:
        data = json.loads(path.read_text(encoding="utf-8"))
        (TARGET_STORIES / path.name).write_text(
            json.dumps(data, ensure_ascii=False, indent=2),
            encoding="utf-8",
        )
        for name in story_image_names(data["story"]):
            needed_images.add(name)
        manifest_entries.append(
            {"file": path.name, "title": data["story"].get("title", "")},
        )

    for name in sorted(needed_images):
        shutil.copy2(SOURCE_IMAGES / name, TARGET_IMAGES / name)

    (TARGET_STORIES / "manifest.json").write_text(
        json.dumps({"count": len(selected), "stories": manifest_entries}, ensure_ascii=False, indent=2),
        encoding="utf-8",
    )

    shutil.rmtree(SOURCE_STORIES)
    shutil.rmtree(SOURCE_IMAGES)

    print(f"OK: {len(selected)} stories, {len(needed_images)} images → backend/")
    for entry in manifest_entries:
        print(f"  - {entry['title']}")


if __name__ == "__main__":
    main()
