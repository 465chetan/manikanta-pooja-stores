import ftplib
import os

server = "ftp.manikantapoojastore.com"
user = "manikantapoojast"
password = "Manikanta123@"

try:
    print(f"Connecting to {server}...")
    ftp = ftplib.FTP(server)
    ftp.login(user, password)
    ftp.cwd("public_html")
    
    # Upload index.html
    with open("index.html", "rb") as f:
        ftp.storbinary("STOR index.html", f)
        print("Uploaded index.html")
        
    # Upload js/home.js
    ftp.cwd("js")
    with open("js/home.js", "rb") as f:
        ftp.storbinary("STOR home.js", f)
        print("Uploaded js/home.js")
        
    ftp.quit()
    print("Done!")
except Exception as e:
    print(f"FTP Error: {e}")
