import ftplib
import sys

server = "ftp.manikantapoojastore.com"
user = "manikantapoojast"
password = "Manikanta123@"

try:
    print(f"Connecting to {server}...")
    ftp = ftplib.FTP(server)
    ftp.login(user, password)
    print("Login successful!")
    print("Current directory:", ftp.pwd())
    print("Directory listing:")
    ftp.retrlines('LIST')
    ftp.quit()
except Exception as e:
    print(f"FTP Error: {e}")
