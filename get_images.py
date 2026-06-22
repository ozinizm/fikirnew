import re

with open("agero_content.html", "r", encoding="utf-8") as f:
    html = f.read()

# Find all <img ...> tags and extract src
img_srcs = re.findall(r'<img[^>]*src=["\']([^"\']+)["\']', html)
print("--- ALL IMAGE SOURCES ---")
for idx, src in enumerate(img_srcs):
    print(f"Image {idx}: {src}")
