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

    # Upload css/style.css
    ftp.cwd("css")
    with open("css/style.css", "rb") as f:
        ftp.storbinary("STOR style.css", f)
        print("Uploaded css/style.css")
    ftp.cwd("..")

    # Upload js/home.js, main.js, products.js
    ftp.cwd("js")
    for jsfile in ["home.js", "main.js", "products.js"]:
        with open(f"js/{jsfile}", "rb") as f:
            ftp.storbinary(f"STOR {jsfile}", f)
            print(f"Uploaded js/{jsfile}")

    ftp.quit()
    print("All done!")
except Exception as e:
    print(f"FTP Error: {e}")
