import re

file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Look for ${...} expressions that contain isolated letters or broken parts
for match in re.finditer(r"\$\{(.*?)\}", content):
    expr = match.group(1)
    if re.search(r'\b[a-zA-Z]\b', expr) and expr not in ['i', 'j', 'k', 'l', 'm', 'n', 'v', 'x', 'y', 'z', 'd', 'h', 's', 't', 'p', 'a', 'm', 'e']:
        # Most of these are okay if they are part of a variable name like item.name
        # But isolated 'n' or 'n('?
        if re.search(r'\b(n|t|s|h|a|v)\b', expr) and not re.search(r'\b(item|day|hotel|exp|h|s|t|p|a|m|e)\.', expr):
            pass
    # Specifically look for ' n '
    if ' n ' in expr:
        line_no = content.count('\n', 0, match.start()) + 1
        print(f"Found ' n ' in line {line_no}: ${{{expr}}}")

print("Check 2 complete.")
