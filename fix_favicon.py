import glob, re

def fix_favicon():
    html_files = glob.glob('*.html')
    admin_files = glob.glob('admin/*.html')
    
    pattern = re.compile(r'<link rel="icon" href="data:image[^>]+>')
    
    for f in html_files:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
        
        # Replace the broken svg icon
        content = pattern.sub('<link rel="icon" href="icons/icon-192.png" type="image/png">', content)
        
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
            
    for f in admin_files:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
        
        # Replace the broken svg icon for admin files (needs ../ prefix)
        content = pattern.sub('<link rel="icon" href="../icons/icon-192.png" type="image/png">', content)
        
        with open(f, 'w', encoding='utf-8') as file:
            file.write(content)
            
    print('Favicon updated in all HTML files.')

fix_favicon()
