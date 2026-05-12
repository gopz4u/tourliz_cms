import sys

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

# Filter only script content
import re
scripts = re.findall(r'<script>(.*?)</script>', content, re.DOTALL)
for i, script in enumerate(scripts):
    l_parens = script.count('(')
    r_parens = script.count(')')
    l_braces = script.count('{')
    r_braces = script.count('}')
    print(f"Script {i}: Parens ({l_parens}/{r_parens}), Braces ({l_braces}/{r_braces})")
    
    # Check balance of each line roughly
    lines = script.split('\n')
    for j, line in enumerate(lines):
        if '(' in line or ')' in line or '{' in line or '}' in line:
            # We can't really check line by line because they span multiple lines
            pass
