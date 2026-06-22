import re

with open("agero_content.html", "r", encoding="utf-8") as f:
    html = f.read()

# Locate the Hero section text
pos = html.find("We make it easy for startups to launch")
if pos != -1:
    # Look for image tags in the 5000 characters following the subtext
    snippet = html[pos:pos+15000]
    imgs = re.findall(r'<img[^>]*>', snippet)
    print("--- HERO SECTION IMAGES ---")
    for img in imgs:
        print(img)
else:
    print("Hero subtext not found.")
