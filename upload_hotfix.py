import ftplib, os
server = "ftp.manikantapoojastore.com"
user = "manikantapoojast"
password = "Manikanta123@"
files = [
    ("js/main.js", "js/main.js"),
    ("css/style.css", "css/style.css")
]
try:
    print(f"Connecting to {server}...")
    ftp = ftplib.FTP(server)
    ftp.login(user, password)
    ftp.cwd("public_html")
    for local, remote in files:
        if not os.path.exists(local): continue
        parts = remote.split("/")
        if len(parts) > 1:
            try: ftp.cwd(parts[0])
            except: 
                ftp.mkd(parts[0]); ftp.cwd(parts[0])
        with open(local, "rb") as f:
            ftp.storbinary(f"STOR {parts[-1]}", f)
        if len(parts) > 1: ftp.cwd("..")
        print(f"  Uploaded: {remote}")
    ftp.quit()
    print("Done!")
except Exception as e:
    print(f"FTP Error: {e}")
