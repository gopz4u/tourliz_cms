file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
script_match = re.search(r"<script>(.*?)<\/script>", content, re.DOTALL)
if script_match:
    script = script_match.group(1)
    
    stack = []
    for i, char in enumerate(script):
        if char == '{':
            stack.append(('{', i))
        elif char == '}':
            if not stack:
                print(f"Excess '}}' at script char {i}")
                # print context
                print(f"Context: {script[max(0, i-50):i+50]}")
            else:
                stack.pop()
    
    if stack:
        for char, pos in stack:
            print(f"Unclosed '{char}' at script char {pos}")
            print(f"Context: {script[max(0, pos-50):pos+50]}")
