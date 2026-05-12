import sys
import re

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\edit.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

scripts = re.findall(r'<script>(.*?)</script>', content, re.DOTALL)
if scripts:
    # Concatenate all scripts
    full_js = "\n".join(scripts)
    # Replace Blade tags with placeholders
    js_code = re.sub(r'\{\{.*?\}\}', '"placeholder"', full_js)
    js_code = re.sub(r'@json\(.*?\)', '{}', js_code)
    js_code = re.sub(r'@\w+', '', js_code)
    with open(r'c:\xampp\htdocs\tourliz_cms\scratch\extracted_edit.js', 'w', encoding='utf-8') as f:
        f.write(js_code)
