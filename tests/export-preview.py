"""Export the real PHP-rendered slider; run after starting the demo installation."""
import re
import urllib.request
from pathlib import Path

root = Path(__file__).resolve().parent.parent
source = urllib.request.urlopen('http://127.0.0.1:9400/').read().decode()
slider = re.search(r'<section class="psf".*?</section>', source, re.S).group()
# Keep the exported page independent of WordPress and its local attachment URLs.
slider = re.sub(r'http://127\.0\.0\.1:9400/wp-content/uploads/[^"\s]+/(photo-[\w-]+?)(?:-\d+x\d+)?\.jpg', r'images/\1.jpg', slider)
slider = re.sub(r'\s(?:srcset|sizes)="[^"]*"', '', slider)
slider = re.sub(r'href="http://127\.0\.0\.1:9400/properties/[^"]*"', 'href="#demo-note"', slider)
html = '''<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Property Slider Free — interactive preview</title>
<link rel="stylesheet" href="../assets/property-slider.css">
<style>body{margin:0;background:#fff;color:#20252b;font-family:Arial,sans-serif}main{max-width:1440px;margin:auto;padding:64px 48px}footer{margin-top:32px;color:#626d76;font-size:14px;line-height:1.6}footer a{color:#287b77}@media(max-width:767px){main{padding:32px 8px}}</style>
<script src="../assets/property-slider.js" defer></script></head><body><main>
''' + slider + '''
<footer id="demo-note"><p>Demonstration listings and contact details. Property links lead here in this static preview.<br>
Illustrative photography: <a href="https://unsplash.com/">Unsplash</a>. <a href="../README.md">Plugin documentation</a>.</p></footer>
</main></body></html>'''
(root / 'preview/index.html').write_text(html)
print('Exported preview/index.html from the live WordPress renderer')
