from PIL import Image
import glob
import os

png_files = glob.glob('images/hero_slide_*.png')
for png in png_files:
    img = Image.open(png).convert('RGB')
    webp_path = png.replace('.png', '.webp')
    # Save at 95% quality for maximum clarity without being a massive file
    img.save(webp_path, 'WEBP', quality=95)
    print(f"High-res re-generated: {webp_path}")
