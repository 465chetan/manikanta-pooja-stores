import glob
import re
import os

for file in glob.glob('**/*.html', recursive=True):
    try:
        with open(file, 'r', encoding='utf-8') as f:
            content = f.read()
            
        icon_path = '../icons/icon-192.png' if '/' in file.replace('\\', '/') else 'icons/icon-192.png'
        
        # The broken SVG string without closing bracket
        broken_str = "<link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>"
        
        # The correct tag
        correct_tag = f'<link rel="icon" href="{icon_path}" type="image/png">'
        
        if broken_str in content:
            content = content.replace(broken_str, correct_tag)
            with open(file, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f'Fixed {file}')
    except Exception as e:
        print(f'Skipping {file}: {e}')
print("Done fixing icons.")
