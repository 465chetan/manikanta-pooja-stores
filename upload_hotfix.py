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

    # Upload product.html
    with open("product.html", "rb") as f:
        ftp.storbinary("STOR product.html", f)
        print("Uploaded product.html")

    # Upload js/cart.js
    ftp.cwd("js")
    with open("js/cart.js", "rb") as f:
        ftp.storbinary("STOR cart.js", f)
        print("Uploaded js/cart.js")

    ftp.quit()
    print("All done!")
except Exception as e:
    print(f"FTP Error: {e}")
