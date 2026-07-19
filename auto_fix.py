import glob, re, os

def fix_mojibake(text):
    pattern = re.compile(r'[^\x00-\x7F]+')
    
    def replacer(match):
        s = match.group(0)
        try:
            b = bytearray()
            for c in s:
                try:
                    b.extend(c.encode('cp1252'))
                except UnicodeEncodeError:
                    if ord(c) < 256:
                        b.append(ord(c))
                    else:
                        raise ValueError('Not cp1252')
            
            fixed = b.decode('utf-8')
            return fixed
        except Exception:
            return s
            
    return pattern.sub(replacer, text)

files = glob.glob('*.html') + glob.glob('admin/*.html')
count = 0
for f_path in files:
    try:
        with open(f_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        fixed_content = fix_mojibake(content)
        
        # If there are changes, save it
        if content != fixed_content:
            with open(f_path, 'w', encoding='utf-8') as f:
                f.write(fixed_content)
            print(f'Fixed mojibake in {f_path}')
            count += 1
    except Exception as e:
        print(f"Error on {f_path}: {e}")

print(f'Total files magically fixed: {count}')
