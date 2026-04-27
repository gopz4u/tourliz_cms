with open(r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\b2b\edit.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(1280, 1290):
    if i < len(lines):
        print(f"Line {i+1}: {repr(lines[i])}")
