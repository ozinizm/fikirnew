import re

with open("agero_content.html", "r", encoding="utf-8") as f:
    html = f.read()

# Let's search for fe71dh-container in the body (where it's used as a class)
matches = [m.start() for m in re.finditer(r'class="[^"]*fe71dh[^"]*"', html)]
print(f"Found {len(matches)} matches in body:")
for idx, start_pos in enumerate(matches):
    start = max(0, start_pos - 100)
    end = min(len(html), start_pos + 1200)
    print(f"\nMatch {idx}:")
    print(html[start:end])
