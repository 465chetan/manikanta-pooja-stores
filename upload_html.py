import ftplib
import glob
import os

server = 'ftp.manikantapoojastore.com'
user = 'manikantapoojast'
password = 'Manikanta123@'

try:
    print('Connecting to FTP...')
    ftp = ftplib.FTP(server)
    ftp.login(user, password)
    
    # Upload root HTML files
    ftp.cwd('public_html')
    for file in glob.glob('*.html'):
        with open(file, 'rb') as f:
            ftp.storbinary(f'STOR {file}', f)
        print(f'Uploaded {file}')
        
    # Upload admin HTML files
    try:
        ftp.cwd('admin')
        for file in glob.glob('admin/*.html'):
            filename = os.path.basename(file)
            with open(file, 'rb') as f:
                ftp.storbinary(f'STOR {filename}', f)
            print(f'Uploaded admin/{filename}')
        ftp.cwd('..')
    except Exception as e:
        print(f'Skipped admin: {e}')
        
    # Upload dashboard HTML files
    try:
        ftp.cwd('dashboard')
        for file in glob.glob('dashboard/*.html'):
            filename = os.path.basename(file)
            with open(file, 'rb') as f:
                ftp.storbinary(f'STOR {filename}', f)
            print(f'Uploaded dashboard/{filename}')
        ftp.cwd('..')
    except Exception as e:
        print(f'Skipped dashboard: {e}')

    ftp.quit()
    print('Done uploading all HTML files!')
except Exception as e:
    print('Error:', e)
