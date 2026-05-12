import sys
import re

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

scripts = re.findall(r'<script>(.*?)</script>', content, re.DOTALL)
if scripts:
    with open(r'c:\xampp\htdocs\tourliz_cms\scratch\extracted.js', 'w', encoding='utf-8') as f:
        # Replace Blade tags with placeholders to avoid syntax errors in JS
        js_code = scripts[0]
        js_code = re.sub(r'\{\{.*?\}\}', '"placeholder"', js_code)
        js_code = re.sub(r'@json\(.*?\)', '{}', js_code)
        js_code = re.sub(r'@\w+', '', js_code)
        f.write(js_code)
