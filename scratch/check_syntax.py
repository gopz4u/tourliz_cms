import sys

filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    content = f.read()

# Very simple check for unbalanced parentheses in JS blocks
in_script = False
line_num = 0
for line in content.split('\n'):
    line_num += 1
    if '<script>' in line: in_script = True
    if '</script>' in line: in_script = False
    
    if in_script:
        l_parens = line.count('(')
        r_parens = line.count(')')
        if l_parens != r_parens:
            # This is too simple because calls can span multiple lines
            pass

# Better: just look for the specific error at line 2363 if possible
# But we don't know where line 2363 is in the source.

# Let's check for route calls that might be malformed
import re
matches = re.finditer(r'route\("([^"]+)"\s*,\s*\[\]\s*,\s*false\)', content)
for m in matches:
    print(f"Found: {m.group(0)}")
