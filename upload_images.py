import ftplib
import glob

server = "ftp.manikantapoojastore.com"
user = "manikantapoojast"
password = "Manikanta123@"

try:
    print(f"Connecting to {server}...")
    ftp = ftplib.FTP(server)
    ftp.login(user, password)
    ftp.cwd("public_html/images")

    webp_files = glob.glob("images/hero_slide_*.webp")
    for file in webp_files:
        filename = file.split('\\')[-1].split('/')[-1] # handle both slashes
        with open(file, "rb") as f:
            ftp.storbinary(f"STOR {filename}", f)
            print(f"Uploaded {filename}")

    ftp.quit()
    print("All done!")
except Exception as e:
    print(f"FTP Error: {e}")
