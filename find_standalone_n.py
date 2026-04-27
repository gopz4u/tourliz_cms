import re

with open(r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Find all 'n' identifiers that are not preceded by a backslash and are standalone words
matches = re.finditer(r'(?<!\\)\bn\b', content)
for m in matches:
    # Get line number
    line_no = content.count('\n', 0, m.start()) + 1
    # Get surrounding context
    start = max(0, m.start() - 20)
    end = min(len(content), m.end() + 20)
    print(f"Line {line_no}: {repr(content[start:end])}")
