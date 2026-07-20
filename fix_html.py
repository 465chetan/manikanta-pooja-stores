import glob
import re

files_fixed = 0
for file in glob.glob('**/*.html', recursive=True):
    try:
        with open(file, 'r', encoding='utf-8') as f:
            content = f.read()
            
        original_content = content
        
        # Regex to remove anything from <text y='.9em' up to </svg>">
        content = re.sub(r'<text y=\'.9em\'.*?</svg>">', '', content, flags=re.DOTALL)
        
        if content != original_content:
            with open(file, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f'Fixed {file}')
            files_fixed += 1
    except Exception as e:
        print(f'Error reading {file}: {e}')
        
print(f'Total files fixed: {files_fixed}')
