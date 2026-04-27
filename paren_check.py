file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = re.finditer(r"<script>(.*?)<\/script>", content, re.DOTALL)
for match_idx, match in enumerate(matches):
    script = match.group(1)
    stack = []
    for i, char in enumerate(script):
        if char == '(': stack.append(('(', i))
        elif char == ')':
            if not stack:
                line_no = script.count('\n', 0, i) + 1
                print(f"Excess ')' at script {match_idx} line {line_no}")
            else: stack.pop()
    if stack:
        for char, pos in stack:
            line_no = script.count('\n', 0, pos) + 1
            print(f"Unclosed '(' at script {match_idx} line {line_no}")
