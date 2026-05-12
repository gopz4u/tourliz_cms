filename = r'c:\xampp\htdocs\tourliz_cms\resources\views\admin\packages\create.blade.php'
with open(filename, 'r', encoding='utf-8') as f:
    for i, line in enumerate(f, 1):
        if line.count("'") % 2 != 0:
            # Check if it's a valid Blade or JS line that spans multiple lines
            if '{{' in line and '}}' in line: continue
            print(f"Line {i}: {line.strip()}")
