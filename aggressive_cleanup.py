import re

file_path = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

def clean_script(match):
    script_content = match.group(1)
    # 1. Replace sequences of multiple spaces/tabs with 1 space
    # BUT we must be careful with backticks where spaces might matter.
    # However, given the corruption, it's worth it.
    
    # Let's target specific known broken patterns first
    script_content = re.sub(r'functio\s+n', 'function', script_content)
    script_content = re.sub(r'retur\s+n', 'return', script_content)
    script_content = re.sub(r'cl\s+ass', 'class', script_content)
    
    # Replace 10+ spaces with a single space (unless inside a string? hard)
    # But usually 10+ spaces in this file ARE corruption.
    script_content = re.sub(r' {10,}', ' ', script_content)
    
    return f"<script>{script_content}</script>"

# Replace all script blocks
new_content = re.sub(r"<script>(.*?)<\/script>", clean_script, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Aggressive cleanup complete.")
