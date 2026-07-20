import glob
import re

for file in glob.glob('**/*.html', recursive=True):
    try:
        with open(file, 'r', encoding='utf-8') as f:
            content = f.read()
            
        if 'auth.js' not in content:
            # Determine path to js
            js_path = '../js/auth.js' if '/' in file.replace('\\', '/') else 'js/auth.js'
            
            # Find js/main.js or ../js/main.js
            # Insert auth.js before it
            new_script = f'<script src="{js_path}" defer></script>\n'
            
            # regex to find main.js script tag
            content, count = re.subn(
                r'(<script[^>]+src=["\'][^"\']*main\.js[^>]*></script>)',
                new_script + r'\1',
                content
            )
            
            if count > 0:
                with open(file, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f'Added auth.js to {file}')
            else:
                print(f'Could not find main.js in {file}')
    except Exception as e:
        print(f'Skipped {file}: {e}')
